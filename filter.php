<?php
   require( 'config.php' );

   $dbhost = DB_HOST;
   $dbuser = DB_USER;
   $dbpass = DB_PASSWORD;
   $schema = DB_SCHEMA;
   
   $commands = "";
   $content = "";
   $message = "";
   $table = "";

   function trim_field( $text, $maxchar=45, $end='...')
   {
        $orignal_text = $text;
        //if( strlen($text) > $maxchar || $text == '' )
        if( strlen($text) > $maxchar )
        {
            $words = preg_split('/\s/', $text);      
            $output = '';
            $i      = 0;
            while( 1 )
            {
                $length = strlen($output)+strlen($words[$i]);
                if ($length > $maxchar)
                {
                   break;
                } 
                else
                {
                   $output .= " " . $words[$i];
                   ++$i;
                }
            }
            $output .= $end;
        } 
        else
        {
            $output = $text;
        }
        return "<span title='$orignal_text'>$output</span>";
   }

   //if( isset( $_POST['submitted'] ) )
   if( 1 )
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
          //echo 'Connected successfully';
       }
       
       $index = $_POST['index'];
       $limit = $_POST['limit'];
       $page  = $_POST['page'];

       $stmt = $conn->prepare( "select * from slog" );
       if( $stmt )
       {
           $stmt->bind_param();
           if( $stmt->execute() )
           {
              $result = $stmt->get_result(); // get the mysqli result
              //if( $stmt->get_result() )
              //{
                 $color = "even";
                 $content .= "<div class='container'>"; // container
                 $table   .= "<div class='table'>";
                 $table   .= "<div class='table-row $color'>".
                                 "<div class='table-row-column table-center'>id</div>" . 
                                 "<div class='table-row-column table-center'>name</div>" . 
                                 "<div class='table-row-column table-center'>email</div>" . 
                                 "<div class='table-row-column table-center'>server_ip</div>" . 
                                 "<div class='table-row-column table-center'>client_ip</div>" . 
                                 "<div class='table-row-column table-center'>server_country</div>" . 
                                 "<div class='table-row-column table-center'>client_country</div>" . 
                                 "<div class='table-row-column table-center'>message</div>" . 
                                 "<div class='table-row-column table-center'>captcha</div>" . 
                                 "<div class='table-row-column table-center'>browser</div>" . 
                                 "<div class='table-row-column table-center'>time_stamp</div>" . 
                                 "<div class='table-row-column table-center'>is_spam</div>" . 
                             "</div>";
                 while( $row = $result->fetch_assoc() )
                 {
                    $color = ( $color == "odd" ) ? "even" : "odd";
                    $table .= "<div class='table-row $color'>".
                                    "<div class='table-row-column table-center'>" . trim_field( $row['id'] ). "</div>" . 
                                    "<div class='table-row-column table-center'>" . trim_field( $row['name'] ) . "</div>" . 
                                    "<div class='table-row-column table-center'>" . trim_field( $row['email'] ) . "</div>" . 
                                    "<div class='table-row-column table-center'>" . trim_field( $row['server_ip'] ) . "</div>" . 
                                    "<div class='table-row-column table-center'>" . trim_field( $row['client_ip'] ) . "</div>" . 
                                    "<div class='table-row-column table-center'>" . trim_field( $row['server_country'] ) . "</div>" . 
                                    "<div class='table-row-column table-center'>" . trim_field( $row['client_country'] ) . "</div>" . 
                                    "<div class='table-row-column table-center'>" . trim_field( $row['message'] ) . "</div>" . 
                                    "<div class='table-row-column table-center'>" . trim_field( $row['captcha'] ) . "</div>" . 
                                    "<div class='table-row-column table-center'>" . trim_field( $row['browser'] ) . "</div>" . 
                                    "<div class='table-row-column table-center'>" . trim_field( $row['time_stamp'] ) . "</div>" . 
                                    "<div class='table-row-column table-center'>" . trim_field( $row['is_spam'] ) . "</div>" . 
                              "</div>";
                    $commands .= "csf -d $row[client_ip] bot;\r\n" ;
                 }
                 $content .= "</div>";
                 $content .= "<div class='row'>
                                 <br/>
                                 Commands<br/>
                                 <textarea style='width: 100%;' rows='10'>$commands</textarea><br/><br/>
                                 <br/>
                              </div>";
                 $content .= "<div class='row'>" . 
                                 "Table<br/>" .
                                 "$table<br/>" .
                             "</div>";
                 $content .= "</div>"; // container
              //}
           }
           else
           {
              echo "Error: " . $conn->error;
           }
           $stmt->close();
           
           echo "<!DOCTYPE html>
                    <html>
                        <head>
                            <title>Filter</title>
                            <style>
                                .container{ width: 100%; margin: auto 0px; padding: auto 0px; }
                                .even{ background-color: #f2f2f2; }

                                .row
                                {
                                   margin: auto 0px !important;
                                   padding: 5px 5px !important;
                                }
                                
                                .table {
                                  width: 100vw;
                                  height: 100vh;
                                  display: flex;
                                  flex-direction: column;
                                }

                                .table-row
                                {
                                  width: 100%;
                                  height: 10vw;
                                  display: flex;
                                  border-bottom: 1px solid #ccc;
                                }

                                .table-header {
                                  font-weight: bold;
                                }

                                .table-row div:first-child {
                                  width: 10% !important;
                                }

                                .table-row-column {
                                  width: 30%;
                                  height: 100%;
                                  display: flex;
                                  align-items: center;
                                  justify-content: center;
                                }
                            </style>
                            <link rel='stylesheet' href='https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css' integrity='sha384-Gn5384xqQ1aoWXA+058RXPxPg6fy4IWvTNh0E263XmFcJlSAwiGgFAW/dAiS6JXm' crossorigin='anonymous'>
                            <script src='https://code.jquery.com/jquery-3.2.1.slim.min.js' integrity='sha384-KJ3o2DKtIkvYIK3UENzmM7KCkRr/rE9/Qpg6aAZGJwFDMVNA/GpGFF93hXpG5KkN' crossorigin='anonymous'></script>
                            <script src='https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.12.9/umd/popper.min.js' integrity='sha384-ApNbgh9B+Y1QKtv3Rn7W3mgPxhU9K/ScQsAP7hUibX39j7fakFPskvXusvfa0b4Q' crossorigin='anonymous'></script>
                            <script src='https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/js/bootstrap.min.js' integrity='sha384-JZR6Spejh4U02d8jOt6vLEHfe/JQGiRRSQQxSfFWpi1MquVdAyjUar5+76PVCmYl' crossorigin='anonymous'></script>
                            <meta name='viewport' content='width=device-width, initial-scale=1'>
                        </head>
                        <body>
                           $content
                       </body>
                    </html>";
       }
       else
       {
           echo "Error: " . $conn->error;
       }

       mysqli_close( $conn );
   }
?>

