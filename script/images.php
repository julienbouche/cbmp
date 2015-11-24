<?php

class ImageTools{
    static function save_image($file_input, $category_name, $img_dir){
        if ((($file_input['size']!=0)) && ($file_input['size']<(209715*5))){
            $imageok = 0 ;
            $root_img_path = $img_dir;
            $temp_img_path = $root_img_path."temp/";
            
            //test if temp directory exists, if not creates it
            if(!file_exists($temp_img_path)){
                mkdir($temp_img_path, 0770, true);
            }
            switch($file_input['type']){
                    case "image/gif" :
                        $nomavatar = $root_img_path.$category_name.".png" ;
                        $nomtemp = $temp_img_path.$category_name.".gif";
                        
                        $imageok = 1 ;
                        break ;
                    case "image/x-png" : 	
                    case "image/png" :
                        //we use png format as the standard format
                        //a copy of the file is sufficient
                        $nomavatar = $root_img_path.$category_name.".png" ;
                        if(file_exists($nomavatar)){
                            //if exists, removes the old file
                            unlink($nomavatar);
                        }
                        
                        if(!copy($file_input['tmp_name'], $nomavatar)){
                            error_log("An error occured while copying location's png file");
                        }
                        
                        //no need to do anything else
                        $imageok = 0;
                        
                        break ;
                    case "image/pjpeg" :				
                    case "image/jpeg" :
                    case "image/jpg" :
                        $nomavatar = $root_img_path.$category_name.".png" ;
                        $nomtemp = $temp_img_path.$category_name.".jpg";

                        $imageok = 1 ;
                        break ;
                    default :
                        echo "Unsupported file format (".$file_input['type'].")" ;
                        break ;
                                                            
            }
            
            if ($imageok == 1){
                //saves image in temp directory
                if (copy($file_input['tmp_name'], $nomtemp)){
                    $tabdim = getimagesize($nomtemp) ;
                    
                    //create standard image from every supported file format
                    switch($file_input['type']){
                        case "image/gif" :
                            $tempfilename = $temp_img_path.$category_name.'.gif';
                            $imbase = imagecreatefromgif ($tempfilename) ;
                            imagepng($imbase, $nomavatar);//dont work well...
                            break ;
                        case "image/jpeg" :
                        case "image/pjpeg" :
                        case "image/jpg" :
                            $tempfilename = $temp_img_path.$category_name.'.jpg';
                            $imbase = imagecreatefromjpeg ($tempfilename) ;
                            ImageTools::convertJPEGToPNG($imbase, $tabdim[0], $tabdim[1], $nomavatar, $tabdim[0], $tabdim[1]);
                            break ;
                        default :
                            echo "Unsupported file format (".$file_input['type'].")" ;
                            break ;
                    }
                    
                    
                    //deletes temp file
                    if(file_exists($tempfilename)){
                        unlink($tempfilename);
                    }
                }
            }
            else{
                error_log("Error while trying to update image.");
            }
        }
    }

    static function convertJPEGToPNG($img_src_resource, $src_w, $src_h, $img_dest_filepath, $dest_width, $dest_height){
        //creates a new image (in memory)
        $destimg = imagecreatetruecolor($dest_width,$dest_height);

        if(imagecopy($destimg, $img_src_resource, 0, 0, 0, 0, $src_w, $src_h)){
            //copy is successful
            
            //if destination file exists
            if(file_exists($img_dest_filepath)){
                //delete the file
                unlink($img_dest_filepath);
            }
            
            //we can save the new image
            if(!imagepng($destimg, $img_dest_filepath, 0)){
                error_log("An error occured while saving category's image");
            }
        }
        else{
            error_log("Error while duplicating image in memory");
        }
        
    }
}


?>