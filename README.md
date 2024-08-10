# mysticWaves_proj

![image](https://github.com/user-attachments/assets/5c421363-9b81-4d0d-b7eb-5a3d2d261dba)

![image](https://github.com/user-attachments/assets/ed833b62-1b89-4c77-acef-2369c48a719f)

Check these things before running the project:

1. db_config.php in Admin Folder

  [.] Database file is present in the DATABASE folder, you just need to import it in phpmyadmin.
  [.] Check the database name, localhost, user name and password.


2. essentials.php in Admin Folder

  [.] define('SITE_URL','http://127.0.0.1/mysticwaves/')

    -> change the above url accordingly, if your project folder name is changed from "hbwebsite" to something else.

  [.] define('UPLOAD_IMAGE_PATH',$_SERVER['DOCUMENT_ROOT'].'/hbwebsite/images/')

    -> change the above url accordingly, if your folder name is changed from "mysticwaves" to something else.
