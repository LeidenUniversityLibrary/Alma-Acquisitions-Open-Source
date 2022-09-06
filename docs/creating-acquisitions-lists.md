!!! NOTE
    This part of the documentation assumes you have followed the instructions in [Part I - Alma Analytics](alma-analytics.md) and [Part II - Installation](installation.md)

## Displaying the latest acquisitions

!!! Important
    Alma Acquisitions - Open Source expects the homepage to display the latest acquisitions; when users visits the application, they will be redirected to the `/latest` page. This behavior can be edited in the `routes/web.php` file.

1. Login with your admin account at `/login`. You will be redirected to `/admin`.
2. You will be presented with a list of available queries coming from Alma Analytics.
3. Click on the 'Create' button next to the list that you want to display on your homepage.
4. Click on `Import XML` to request the latest data from Alma Analytics for this acquisitions list.
5. In the `Acquisitions list name` type the name you would like to display when the users land on the home page. At Leiden University Libraries we used 'Latest'.
6. In the `Acquisitions list URL` you **must** type `latest`.
7. Click on `Create acquisitions list`

If you navigate to the homepage, you should now see your library's latest acquisitions.

!!! danger
    You cannot delete the `latest` acquisitions list, but you can modify it. Be careful not to edit the `Acquisitions list's URL path` as AAOS always uses `latest` as the path to load the homepage.

## Creating additional acquisitions lists

Once you have created the `latest` acquisitions list, you are free to create as many acquisitions list you might need. First create the list in Alma Analytics [as explained in the documentation](alma-analytics.md#creating-an-acquisitions-list-for-a-specific-subject-in-alma-analytics), amd then follow steps 1 to 7 making sure to give a different name to your acquisitions list and URL path than `latest`.
