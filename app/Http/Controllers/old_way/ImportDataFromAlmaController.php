<?php

namespace App\Http\Controllers\old_way;

use App\Http\Controllers\Controller;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;
use Throwable;
use function App\Http\Controllers\mb_convert_encoding;
use function redirect;

class ImportDataFromAlmaController extends Controller
{
    /**
     * This controller executes all the steps required to load new acquisitions from the sftp server.
     * It converts the files into json, and then into database tables.'
     * It should run daily with a cronjob.
     *
     * @param Request $request
     * @return RedirectResponse
     */
    public function __invoke(Request $request): RedirectResponse
    {
        Log::notice("Importing acquisitions from server.");
        try {

            //NOTE: delete currently collected files
            $old_imported_files = Storage::disk('local')->allFiles('import/');
            Storage::delete($old_imported_files);

            Log::info("Deleted old imported files");

            //NOTE: import from the in/acquisitions folder on the remote server
            $file_list = Storage::disk('sftp')->allFiles('in/acquisitions/');

            foreach ($file_list as $key => $value) {

                Storage::disk('local')->put(str_replace("in/acquisitions/", "\\import\\", $value), Storage::disk('sftp')->get($value));

            }

            Log::notice("Imported acquisitions from server. Now converting files from UTF-16 to UTF-8.");

            $old_converted_files = Storage::disk('local')->allFiles('converted/');
            Storage::delete($old_converted_files);

            Log::info("Deleted old converted files");


            $sftp_imported_files = Storage::disk('local')->allFiles('import/');

            foreach ($sftp_imported_files as $key => $value) {

                $contents = Storage::disk('local')->get($value);

                $converted_contents = mb_convert_encoding($contents, "UTF-8", "UTF-16");
                Storage::disk('local')->put(str_replace("import/", "\\converted\\", $value), $converted_contents);

            }

            Log::notice("Converted files from UTF-16 to UTF-8. Now converting files from txt to JSON.");

            $old_json_files = Storage::disk('local')->allFiles('json/');
            Storage::delete($old_json_files);

            Log::info("Deleted old JSON files");

            $converted_files = Storage::disk('local')->allFiles('converted/');

            foreach ($converted_files as $key => $value) {

                $tsv = Storage::disk('local')->get($value);
                $tabDelimitedLines = explode("\r\n", $tsv);
                $myArray = array();

                foreach ($tabDelimitedLines as $lineIndex => $line) {
                    $fields = explode("\t", $line);

                    foreach ($fields as $fieldIndex => $field) {

                        if ($lineIndex == 0) {
                            // assuming first line is header info
                            $headers[] = $field;
                        } else {
                            // put the other lines into an array
                            // in whatever format you want
                            $myArray[$lineIndex - 1][$headers[$fieldIndex]] = $field;
                        }
                    }
                }
                // Get only get the first 100 results. In our imported json files the most recent acquisitions are at the bottom of the lists.
                $output = array_slice($myArray, 0, 100, false);

                $json = json_encode($output);

                Storage::disk('local')->put(str_replace(array("converted/", ".txt"), array("\\json\\", '.json'), $value), $json);
            }

            Log::notice("Converted to JSON. Now removing old entries from the MySQL database and importing the new data.");
            $mergeable_files = Storage::disk('local')->allFiles('json/');

            foreach ($mergeable_files as $file) {

                $table_name = basename($file);

                //if the table does not exist, create one
                if (!Schema::connection('mysql')->hasTable($table_name)) {
                    Log::notice("Table does not exist in MySQL database. Importing: " . $table_name);
                    Schema::connection('mysql')->create($table_name, function (Blueprint $table) {

                        $table->id();
                        $table->string('MMS Id')->nullable();
                        $table->text('Title')->nullable();
                        $table->text('Author')->nullable();
                        $table->text('Publisher')->nullable();
                        $table->string('Publication Date')->nullable();
                        $table->string('Resource Type')->nullable();
                        $table->dateTime('Creation Date')->nullable();
                        $table->text('Subjects')->nullable();
                        $table->text('Start Range')->nullable();
                    });
                } else {
                    //else, if it exists already, empty it, so it can be filled with new data.
                    Log::notice("Table already exist in MySQL database. Emptying table: " . $table_name);
                    DB::table($table_name)->truncate();
                }

                $contents_of_file = Storage::get($file);

                $file_array = json_decode($contents_of_file, true);

                // Insert new entries
                Log::notice("Importing new acquisitions for: " . $table_name);

                DB::table($table_name)->insert($file_array);

            }

            return redirect()->route('home')->with("success", "Successfully imported and converted all acquisitions lists.");

        } catch (Exception $e) {

            Log::error('Failed to import and convert the acquisitions lists: ' . $e);
            return redirect()->route('home')->with("error", "Something went wrong: failed to import and convert the acquisitions lists. Please try again later.");
            //TODO Send notification to teams.

        }
    }
}
