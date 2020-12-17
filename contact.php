<?php

   //ini_set('display_errors', 1);
   //ini_set('display_startup_errors', 1);
   //error_reporting(E_ALL);

   $content = "";   
   $message = "";

   if( file_exists( "config.php" ) )
   {
      //echo "The file config.php exists";
      /* This will give an error. Note the output
         above, which is before the header() call */
      require( 'config.php' );
   }
   else
   {
       die( "Error - Complete setup" );
   }

   $dbhost = DB_HOST;
   $dbuser = DB_USER;
   $dbpass = DB_PASSWORD;
   $schema = DB_SCHEMA;

   if( isset( $_POST['submitted'] ) )
   {
       $conn = mysqli_connect( $dbhost, $dbuser, $dbpass, $schema );

       if( !$conn )
       {
          echo "Error: Unable to connect to MySQL." . PHP_EOL . "<br/>";
          echo "Debugging errno: " . mysqli_connect_errno() . PHP_EOL . "<br/>";
          echo "Debugging error: " . mysqli_connect_error() . PHP_EOL . "<br/>";
          die( 'Could not connect: ' . mysqli_error( $conn ) );
       }
       else
       {
          //$message .= 'Connected successfully';
       }

       $content_name    = $_POST['name'];
       $content_email   = $_POST['email'];
       $content_server_ip = $_POST['server_ip'];
       $content_client_ip = $_POST['client_ip'];
       $content_captcha = $_POST['captcha'];
       $content_message = $_POST['message'];
       $content_browser = $_POST['browser'];
       $content_server_country = "";
       $content_client_country = "";

       $stmt = $conn->prepare( "INSERT INTO slog ( name, email, server_ip, client_ip, server_country, client_country, message, captcha, browser ) VALUES ( ?, ?, ?, ?, ?, ?, ?, ?, ? )" );
       if( $stmt )
       {
           $stmt->bind_param( "sssssssss", $content_name, $content_email, $content_server_ip, $content_client_ip, $content_server_country, 
                                           $content_client_country, $content_message, $content_captcha, $content_browser );
           if( $stmt->execute() )
           { 
              $message .= "Thank you for your submission.<br/>";
           }
           else
           {
              //$message .= "Error: " . $conn->error;
           }
           $stmt->close();
       }

       mysqli_close( $conn );
   }
   else
   {
   }

   $content .= "<!DOCTYPE html>
                    <html>
                        <head>
                            <title>Contact Us</title>
                            <link rel='stylesheet' href='https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css' integrity='sha384-Gn5384xqQ1aoWXA+058RXPxPg6fy4IWvTNh0E263XmFcJlSAwiGgFAW/dAiS6JXm' crossorigin='anonymous'>
                            <script src='https://code.jquery.com/jquery-3.2.1.slim.min.js' integrity='sha384-KJ3o2DKtIkvYIK3UENzmM7KCkRr/rE9/Qpg6aAZGJwFDMVNA/GpGFF93hXpG5KkN' crossorigin='anonymous'></script>
                            <script src='https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.12.9/umd/popper.min.js' integrity='sha384-ApNbgh9B+Y1QKtv3Rn7W3mgPxhU9K/ScQsAP7hUibX39j7fakFPskvXusvfa0b4Q' crossorigin='anonymous'></script>
                            <script src='https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/js/bootstrap.min.js' integrity='sha384-JZR6Spejh4U02d8jOt6vLEHfe/JQGiRRSQQxSfFWpi1MquVdAyjUar5+76PVCmYl' crossorigin='anonymous'></script>
                            <meta name='viewport' content='width=device-width, initial-scale=1'>
                            <script src='ua-parser.js'></script>
                            <style>
                               .form-control{ width: 100%; }
                               .form-group{ width: 100%; }
                               .submit{ background: #f8f9fa; }
                            </style>
                        </head>
                        <body>
                           <div class='container'>
                               <div class='row'>
                                   $message<br/>
                               </div>
                               <div class='row'>
                                   <form class='form-control' name='contact-us' method='post' action='contact.php'>
                                      <input type='hidden' name='server_ip' value='" . getenv('SERVER_ADDR') . "' /> 
                                      <input type='hidden' name='client_ip' value='" . getenv('REMOTE_ADDR') . "' /> 
                                      <input type='hidden' id='browser' name='browser' value='' />
                                      <input type='hidden' name='captcha' value='' />
                                      <input type='hidden' name='submitted' value='1' /> 
                                      <div class='form-group'>
                                         Name: <input class='form-control' type='text' name='name' required /><br/>
                                      </div>
                                      <div class='form-group'>
                                         Email: <input class='form-control' type='text' name='email' required /><br/>
                                      </div>
                                      <div class='form-group'>
                                         Message: <input class='form-control' type='text' name='message' required /><br/>
                                      </div>
                                      <div class='form-group'>
                                         <input class='form-control submit' type='submit' value='Submit' /><br/>
                                      </div>
                                   </form>
                               </div>
                           </div>
                           <script>
                              var parser = new UAParser();
                              var brodserHidden = document.getElementById('browser').value = JSON.stringify( parser.getResult() );
                           </script>
                           <br/>
                        </body>
                    </html>
                ";
   echo $content;
?>
