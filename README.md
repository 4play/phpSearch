1. Overview

This Documentation describes the installation and configuration steps of phpSearch.

2. Requirements

Please ensure that your server meets the following minimum requirements:

Software	Modules
PHP 7, or higher	MySQLnd, cURL, OpenSSL, mbstring
Apache 2, or higher	mod_rewrite
MySQL 5, or higher	
The following services are also required:

Service	Information
Bing Search API Key	For a complete experience, we recommend subscribing to the S1 plan.

3. Installation

After you've downloaded the product and extracted the contents from the ZIP package, you can start the installation process.

3.1. Importing the database

Create a new MySQL database (optional).
Create a new MySQL username and password (optional).
Import the Database.sql file from the MySQL folder into your MySQL database.
3.2. Setting-up and uploading the files

With a text editor open the Script/app/includes/config.php file and update the values YOURDBUSER, YOURDBNAME, YOURDBPASS, https://example.com with your own information.
Upload the contents of the Script folder on the location where you want the product to be installed at.
Set the CHMOD to 755, 775 or 777 (depending on the server configuration) to the /public/uploads folder and its subfolders.
3.3. Finishing the installation

You're almost done, the last step is to access your Admin Panel (via https://example.com/admin) using the default credentials:

Username	admin
Password	password
Don't forget to change your Admin Panel password once you've logged-in.
4. Configuration

Most of the phpSearch's settings are pretty straight forward and self explanatory, but some of them require more advanced steps to get them working, which are covered in the next chapter.

4.1. Bing Search API

Go to Bing Web Search API portal and create a new Bing Search v7 API.
Pick up the plan that suit your needs, for example the S1 plan, but you can use other plans as well (for example if you plan on using the Web search only, then you can choose the S2 plan).
Once you've enabled the API, copy and add the API Key 1 in the Admin Panel > Search section.
5. FAQ

5.1. General

I have a support inquiry, a question or a problem, how can I contact you?
You can contact us here.
What hosting do you recommend?
We recommend using DigitalOcean (free $10 on signup), as they offer great performance and flexibility at an affordable price.
Is installation included in the price?
No, installation is not included. We offer installation services for an extra fee. Contact Us.
How does Safe Ads work?
When enabled, Safe Ads will only show your the ad units when the Strict Safe Search filter is used.
How does Search Answers work?
When enabled, Search Answers will display Images, Videos and News on the Web Results.
How does Search Entities work?
When enabled, Search Entities will show a rich content result in the sidebar.
How does Related Searches work?
When enabled, Related Searches will show display related search suggestions.
How does Search Suggestions work?
When enabled, Search Suggestions will show search suggestions while typing in the search box.
How does Searches / IP work?
Searches / IP limits the number of searches a user is allowed to make per day.
How does Suggestions / IP work?
Suggestions / IP limits the number of suggestions an user can receive per day.
How does Search Privacy work?
When enabled, Search Privacy will load all the external resources trough a proxy on your server, so that the end user is not being tracked by any 3rd parties.

5.2. Installation

Why Permalinks are not working on my site?
Make sure you've uploaded the .htaccess files that comes with the software and that you have mod_rewrite enabled on your server.
I can't find the .htaccess files, why?
You're probably using MacOS, which by default is hiding the .htaccess files, enable the option to see hidden files.
How can I add a new site language?
You can add a new site language by copying your new language file in the /app/languages folder.
My website returns a blank page, why?
This generally happens when one of the server requirements is not met, you can check your current server configuration by accessing https://example.com/i.php.

5.3. Configuration

How can I change my website's logo?
You can change the Logo or the Site title from the Admin Panel > Appearance.
How can I change my website's favicon?
You can change the favicon from Admin Panel > Appearance.
How can I disable a search type?
If you want to disable the Image search for example, you can do it by going to Admin Panel > Search section and set the value of Images results / page to 0, same thing applies for the rest of the search types.
How can I remove the Powered by tag?
You can remove the Powered by from the /public/themes/search/views/shared/footer.php file.
How can I change a text string on my website?
You can change any text from the language files located in the /app/languages folder.
How can I add more backgrounds to my website?
Upload your .JPG backgrounds in the /public/uploads/backgrounds folder.
