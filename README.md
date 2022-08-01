[![Contributors][contributors-shield]][contributors-url]
[![Forks][forks-shield]][forks-url]
[![Stargazers][stars-shield]][stars-url]
[![Issues][issues-shield]][issues-url]
[![GNU V.3 License][license-shield]][license-url]

<!-- Leiden University Libraries Logo -->
<br />
<div align="center">
  <a href="https://github.com/LeidenUniversityLibrary/Alma-Acquisitions-Open-Source">
    <img src="README/ul_logo.png" alt="Leiden University Libraries Logo" height="160">
  </a>

<h3 align="center">Alma Acquisitions - Open Source</h3>

  <p align="center">
    Display your library's latest acquisitions using data coming from Alma.
    <br />
    <!--TODO-->
    <a href="#"><strong>Explore the docs » (coming soon)</strong></a>
    <br />
    <br />
    <a href="https://acquisitions.library.universiteitleiden.nl">View Demo</a>
    ·
    <a href="https://github.com/LeidenUniversityLibrary/Alma-Acquisitions-Open-Source/issues">Report Bug</a>
    ·
    <a href="https://github.com/LeidenUniversityLibrary/Alma-Acquisitions-Open-Source/issues">Request Feature</a>
  </p>
</div>

<details>
  <summary>Table of Contents</summary>
  <ol>
    <li>
      <a href="#about-the-project">About The Project</a>
      <ul>
        <li><a href="#built-with">Built With</a></li>
      </ul>
    </li>
    <li>
      <a href="#getting-started">Getting Started</a>
      <ul>
        <li><a href="#prerequisites">Prerequisites</a></li>
        <li><a href="#installation">Installation</a></li>
      </ul>
    </li>
    <li><a href="#usage">Usage</a></li>
    <li><a href="#roadmap">Roadmap</a></li>
    <li><a href="#contributing">Contributing</a></li>
    <li><a href="#license">License</a></li>
    <li><a href="#contact">Contact</a></li>
    <li><a href="#acknowledgments">Acknowledgments</a></li>
  </ol>
</details>

## About The Project

[![Alma Acquisitions - Open Source][product-screenshot]](https://acquisitions.library.universiteitleiden.nl)

Alma Acquisitions - Open Source is a public version of the [Leiden University Libraries New Acquisitions](https://acquisitions.library.universiteitleiden.nl) app.

AAOS was created to display the latest acquisitions made at Leiden University Libraries: e-books, journals, e-journals, etc. It was designed to allow the library's employees to manage the acquisitions lists via an clear and user-friendly interface, rather than depending on the actions of a developer to create or modify them.

### Features

* An admin panel for creating, updating, and deleting displayable acquisitions lists
* An easy-to-customize environment
* Responsive front-end

### Built With

* [![Laravel][Laravel.com]][Laravel-url]
* [![Bootstrap][Bootstrap.com]][Bootstrap-url]

## Getting Started

Built with Laravel 8, AAOS follows the same installations steps of any application built using this framework. Make sure you have PHP >v.7.3.33, a database, and off you go!

### Prerequisites

AAOS has few requirements:

* PHP V.7.3.33 and above
* Composer
* MySQL
* An **Analytics - Production Read-only** API key from the [ExLibris Developer Network](https://developers.exlibrisgroup.com/) coming from an account bound to your institution.

### Installation

1. Download the release package
2. Unzip the release package to a folder of your choosing
3. Composer install to install the required packages
4. Copy the .env.example file and rename it .env
5. Edit the .env file with your details and the name of your MySQL database
6. Use the command ```php artisan migrate:fresh --seed``` to populate the database with some sample data, and a user account for experimenting
7. ```php artisan serve``` to see your local copy of Alma Acquisitions - Open Source in action.

## Usage

AAOS displays data coming from Alma Analytics. If you want to change the data that is displayed in this application, you must edit your queries in Alma Analytics.

For example:

If your institution wants only to display physical books, you will have to edit the relative saved query in Alma Analytics.

Similarly, if you would prefer to display only electronic books, you will have to remove the physical books from the query.

*For detailed instructions on how to create Alma Analytics queries that can be used by Alma Acquisitions - Open Source, please refer to the [Documentation](https://example.com)*

## Roadmap

* [ ] Google Books covers
* [ ] Frontend redesign

See the [open issues](https://github.com/LeidenUniversityLibrary/Alma-Acquisitions-Open-Source/issues) for a full list of proposed features (and known issues).

## Contributing

Contributions are what make the open source community such an amazing place to learn, inspire, and create. Any contributions you make are **greatly appreciated**.

If you have a suggestion that would make this better, please fork the repo and create a pull request. You can also simply open an issue with the tag "enhancement".
Don't forget to give the project a star! Thanks again!

1. Fork the Project
2. Create your Feature Branch (`git checkout -b feature/AmazingFeature`)
3. Commit your Changes (`git commit -m 'Add some AmazingFeature'`)
4. Push to the Branch (`git push origin feature/AmazingFeature`)
5. Open a Pull Request

## License

Distributed under the GNU General Public License v3.0 License. See `LICENSE.txt` for more information.

## Contact

Leiden University Libraries - [@ubleiden](https://twitter.com/ubleiden) - [contact us](https://www.library.universiteitleiden.nl/about-us/contact)

Project Link: [https://github.com/LeidenUniversityLibrary/Alma-Acquisitions-Open-Source](https://github.com/LeidenUniversityLibrary/Alma-Acquisitions-Open-Source)

## Acknowledgments

* [Stack Overflow](https://stackoverflow.com/)
* [Alma -- Ex Libris user community discussion list for Alma](https://exlibrisusers.org/listinfo/alma)

<!-- MARKDOWN LINKS & IMAGES -->
<!-- https://www.markdownguide.org/basic-syntax/#reference-style-links -->
[contributors-shield]: https://img.shields.io/github/contributors/LeidenUniversityLibrary/Alma-Acquisitions-Open-Source.svg?style=for-the-badge
[contributors-url]: https://github.com/LeidenUniversityLibrary/Alma-Acquisitions-Open-Source/graphs/contributors
[forks-shield]: https://img.shields.io/github/forks/LeidenUniversityLibrary/Alma-Acquisitions-Open-Source.svg?style=for-the-badge
[forks-url]: https://github.com/LeidenUniversityLibrary/Alma-Acquisitions-Open-Source/network/members
[stars-shield]: https://img.shields.io/github/stars/LeidenUniversityLibrary/Alma-Acquisitions-Open-Source.svg?style=for-the-badge
[stars-url]: https://github.com/LeidenUniversityLibrary/Alma-Acquisitions-Open-Source/stargazers
[issues-shield]: https://img.shields.io/github/issues/LeidenUniversityLibrary/Alma-Acquisitions-Open-Source.svg?style=for-the-badge
[issues-url]: https://github.com/LeidenUniversityLibrary/Alma-Acquisitions-Open-Source/issues
[license-shield]: https://img.shields.io/github/license/LeidenUniversityLibrary/Alma-Acquisitions-Open-Source.svg?style=for-the-badge
[license-url]: https://github.com/LeidenUniversityLibrary/Alma-Acquisitions-Open-Source/blob/master/LICENSE.txt
[linkedin-shield]: https://img.shields.io/badge/-LinkedIn-black.svg?style=for-the-badge&logo=linkedin&colorB=555
[linkedin-url]: https://linkedin.com/in/linkedin_username
[product-screenshot]: README/screenshot.png
[Laravel.com]: https://img.shields.io/badge/Laravel-FF2D20?style=for-the-badge&logo=laravel&logoColor=white
[Laravel-url]: https://laravel.com
[Bootstrap.com]: https://img.shields.io/badge/Bootstrap-563D7C?style=for-the-badge&logo=bootstrap&logoColor=white
[Bootstrap-url]: https://getbootstrap.com
