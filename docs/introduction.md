**Alma Acquisitions - Open Source (AAOS)** is the open-source version of the [Acquisitions - New books at the library](https://acquisitions.library.universiteitleiden.nl) in use at Leiden University Libraries.

AAOS was created to display the latest acquisitions made at Leiden University Libraries: e-books, journals, e-journals, etc. It was designed to allow the library's employees to manage the acquisitions lists via an clear and user-friendly admin interface, rather than depending on the actions of a developer to create or modify them.

``` mermaid
graph LR
A[Alma Analytics API] --> |1. requests data from| B[Alma Analytics];
B --> |2. requests data from| C[Oracle Analytics Server]
C --> |3. sends data to| B;
B --> |4. sends data to| A;
A --> |5. finally, sends data to | D[Alma Acquisitions Open Source];
```

## Features

* An admin panel for creating, updating, and deleting displayable acquisitions lists
* An easy-to-customize environment
* Responsive front-end for consulting on any device
