<?php

namespace App\Http\Controllers\old_way;

use App\Http\Controllers\Controller;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;
use Throwable;
use function App\Http\Controllers\mb_convert_encoding;
use function redirect;

class IndividualStepsToImportAcquisitionsController extends Controller
{

    /**
     * This controller allows us to execute the individual steps for importing acquisitions from the SFTP server.
     * Unlike the ImportDataFromAlmaController, this controller here is not automated and does not use a cronjob.
     * The individual functions can be executed by an admin in the Admin blade views.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /*
     *
     *   SECTION Individual steps for importing acquisitions from the sftp in the right order.
     *   NOTE   1) import from Alma
     *          2) convert from UTF16 to UTF8
     *          3) Make JSON files and get only the latest 100 acquisitions
     *          4) Import into database
     */

    public function importFromAlmaFTP(): RedirectResponse
    {
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

        } catch (Exception $e) {
            Log::error("Failed to import acquisitions lists from Alma: " . $e);
            return redirect()->route('home')->with("error", "Failed to import acquisitions lists from Alma.");

        }

        return redirect()->route('home')->with("success", "Successfully imported acquisitions lists from Alma.");

    }

    public function convertFilesFromUTF16toUTF8(): RedirectResponse
    {

        try {
            $old_converted_files = Storage::disk('local')->allFiles('converted/');
            Storage::delete($old_converted_files);

            Log::info("Deleted old converted files");

            $sftp_imported_files = Storage::disk('local')->allFiles('import/');

            foreach ($sftp_imported_files as $key => $value) {

                $contents = Storage::disk('local')->get($value);

                $converted_contents = mb_convert_encoding($contents, "UTF-8", "UTF-16");
                Storage::disk('local')->put(str_replace("import/", "\\converted\\", $value), $converted_contents);

            }
        } catch (Exception $e) {
            Log::error("Failed to convert acquisitions lists to UTF8: " . $e);
            return redirect()->route('home')->with("error", "Failed to convert acquisitions lists to UTF8.");

        }

        return redirect()->route('home')->with("success", "Successfully converted acquisitions lists to UTF8.");

    }

    public function createJSONFilesFromTXT(): RedirectResponse
    {
        try {
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

                Storage::disk('local')->put(str_replace(array("converted/", ".txt"), array("\\json\\"), $value), $json);

            }
        } catch (Exception $e) {

            Log::error("Failed to create JSON files: " . $e);
            return redirect()->route('home')->with("error", "Failed to create JSON files.");

        }

        return redirect()->route('home')->with("success", "Successfully created JSON files.");

    }

    /* NOTE: Each json file creates a new table filled with its content. */

    public function refreshDatabase(): RedirectResponse
    {

        try {

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

                Log::notice("Importing new acquisitions for: " . $table_name . " was successful.");
            }

        } catch (Exception $e) {

            Log::error("Unable to refresh acquisitions databases: " . $e);
            return redirect()->route('home')->with("error", "Failed to import data into the database. See logs for additional information.");
            //TODO Send notification to teams.
        }

        return redirect()->route('home')->with("success", "Successfully replaced acquisitions in the database.");
    }

    //!SECTION
}
