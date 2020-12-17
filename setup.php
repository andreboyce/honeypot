<?php

   ini_set('display_errors', 1);
   ini_set('display_startup_errors', 1);
   error_reporting(E_ALL);

   $content = "";
   $message = "";

   if( file_exists( "config.php" ) )
   {
      //echo "The file config.php exists";
      /* This will give an error. Note the output
         above, which is before the header() call */
      header('Location: contact.php');
   }
   else
   {
      //echo "The file $filename does not exist";
   }

   if( isset( $_POST['submitted'] ) )
   {
       $db_host     = $_POST['db_host'];
       $db_schema   = $_POST['db_schema'];
       $db_username = $_POST['db_username'];
       $db_password = $_POST['db_password'];

       $conn = mysqli_connect( $db_host, $db_username, $db_password, $db_schema );

       if( !$conn )
       {
          $message .= "Error: Unable to connect to MySQL." . PHP_EOL . "<br/>";
          $message .= "Debugging errno: " . mysqli_connect_errno() . PHP_EOL . "<br/>";
          $message .= "Debugging error: " . mysqli_connect_error() . PHP_EOL . "<br/>";
          //die( 'Could not connect: ' . mysqli_error( $conn ) );
       }
       else
       {
           $message .= 'Connected successfully<br/>';
           $stmt = $conn->prepare( "CREATE TABLE IF NOT EXISTS `slog` (
                                      `id` int(11) NOT NULL,
                                      `name` tinytext NOT NULL,
                                      `email` tinytext NOT NULL,
                                      `server_ip` tinytext NOT NULL,
                                      `client_ip` tinytext NOT NULL,
                                      `server_country` tinytext NOT NULL,
                                      `client_country` tinytext NOT NULL,
                                      `message` text NOT NULL,
                                      `captcha` tinytext NOT NULL,
                                      `browser` text NOT NULL,
                                      `time_stamp` datetime(6) NOT NULL DEFAULT current_timestamp(6),
                                      `is_spam` tinyint(1) NOT NULL DEFAULT 0
                                    ) ENGINE=InnoDB DEFAULT CHARSET=latin1;" );
           if( $stmt )
           {
               //$stmt->bind_param();
               if( $stmt->execute() )
               { 
                   $message .= "SQL Success.<br/>";
                   $config_file = fopen( "config.php", "w" ) or die( "Unable to open file!" );
                   $txt = "<?php" . PHP_EOL . PHP_EOL .
                          "// host" . PHP_EOL .
                          "define( 'DB_HOST',     '$db_host' );" . PHP_EOL . PHP_EOL .
                          "// schema" . PHP_EOL .
                          "define( 'DB_SCHEMA',   '$db_schema' );" . PHP_EOL . PHP_EOL .
                          "// username" . PHP_EOL .
                          "define( 'DB_USER',     '$db_username' );" . PHP_EOL . PHP_EOL .
                          "// password" . PHP_EOL .
                          "define( 'DB_PASSWORD', '$db_password' );" . PHP_EOL . PHP_EOL .
                          "?>";
                   fwrite( $config_file, $txt);
                   fclose( $config_file );
                   
                   if( file_exists( "config.php" ) )
                   {
                      $message .= "Successfully created config file.<br/>";
                      header('Location: contact.php');
                   }
                   else
                   {
                      $message .= "Failed creating config file.<br/>";
                   }
               }
               else
               {
                  $message .= "Error: " . $conn->error;
               }
               $stmt->close();
           }
           else
           {
               $message .= "Error Preparing Statement: " . $conn->error;
           }
           mysqli_close( $conn );
       }
   }

   $content .= "<!DOCTYPE html>
                    <html>
                        <head>
                            <title>Setup</title>
                            <link rel='stylesheet' href='https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css' integrity='sha384-Gn5384xqQ1aoWXA+058RXPxPg6fy4IWvTNh0E263XmFcJlSAwiGgFAW/dAiS6JXm' crossorigin='anonymous'>
                            <script src='https://code.jquery.com/jquery-3.2.1.slim.min.js' integrity='sha384-KJ3o2DKtIkvYIK3UENzmM7KCkRr/rE9/Qpg6aAZGJwFDMVNA/GpGFF93hXpG5KkN' crossorigin='anonymous'></script>
                            <script src='https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.12.9/umd/popper.min.js' integrity='sha384-ApNbgh9B+Y1QKtv3Rn7W3mgPxhU9K/ScQsAP7hUibX39j7fakFPskvXusvfa0b4Q' crossorigin='anonymous'></script>
                            <script src='https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/js/bootstrap.min.js' integrity='sha384-JZR6Spejh4U02d8jOt6vLEHfe/JQGiRRSQQxSfFWpi1MquVdAyjUar5+76PVCmYl' crossorigin='anonymous'></script>
                            <meta name='viewport' content='width=device-width, initial-scale=1'>
                            <style>
                               .form-control{ width: 100%; }
                               .form-group{ width: 100%; }
                            </style>
                        </head>
                        <body>
                           <div class='container'>
                              <div class='row'>
                                 $message
                              </div>
                              <div class='row'>
                                   <form class='form-control' name='contact-us' method='post' action='setup.php'>
                                      <input type='hidden' name='submitted' value='1' />

                                      <div class='form-group'>
                                         Hostname: <input class='form-control' type='text' name='db_host' value='localhost' required /><br/>
                                      </div>
                                      
                                      <div class='form-group'>
                                         DBSchema: <input class='form-control' type='text' name='db_schema' value='' required /><br/>
                                      </div>
                                      
                                      <div class='form-group'>
                                         Username: <input class='form-control' type='text' name='db_username' value='' required /><br/>
                                      </div>
                                      
                                      <div class='form-group'>
                                         Password: <input class='form-control' type='password' name='db_password' value='' required /><br/>
                                      </div>
                                      
                                      <div class='form-group'>
                                         <input class='form-control' type='submit' value='submit' /><br/>
                                      </div>
                                   </form>
                              </div>
                           </div>
                        </body>
                    </html>";
   
   echo $content;
?>
