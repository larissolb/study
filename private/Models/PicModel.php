<?php
namespace Larissolb\Rainbow\Models;
use Larissolb\Rainbow\Base\DBConnection;
use Larissolb\Rainbow\Base\Response;


class PicModel 
{

const SIZE_ERROR = "SIZE_ERROR";
const TYPE_ERROR = "TYPE_ERROR";
const LOAD_SUCCESS = "LOAD_SUCCESS";
const NO_PIC = "NO_PIC";
const COMMENT_SAVED =  "COMMENT_SAVED";
const COMMENT_EMPTY =  "COMMENT_EMPTY";
const LIKE =  "LIKE";
const GO_AUTH = "GO_AUTH";

  
private $db;
protected $response;
    
    public function __construct() 
            {
            $this->db = DBConnection::getDBConnection();
            $this->response = new Response();
    }


    public function getComments($id) {
             //получение комментариев к картинке
        $sql = "SELECT comment, Users_login, Date FROM Comments WHERE Pics_id=:Pics_id";
        $params = [
            'Pics_id'=>$id
                ];
        $statement = $this->db->execute($sql, $params, true);
        
        $comments = [];
        
        foreach ($statement as $comment) {

            array_push($comments, $comment);     
  }
  
        return $comments;
    }
    
        public function getLikes($id) {
             //получение комментариев к картинке
        $sql = "SELECT `like` FROM Pics WHERE id=:id";
        $params = [
            'id'=>$id
                ];
        $statement = $this->db->execute($sql, $params, FALSE);
        $likes = $statement['like'];

        $_SESSION['idPic'] = $id;
          
        return $likes;
    }
    
    public function getLastLoadPics() {
        $sql = "SELECT *  FROM Pics ORDER BY id ASC";
        $last_pics = $this->db->queryAll($sql);

        foreach($last_pics as $arr){
            $last_pic = $arr;
        }
        
        return $last_pic;
    }
    
    public function getRandomPics() {
        $sql = "SELECT id, img_path  FROM Pics ORDER BY id DESC LIMIT 4";
        $pics_arr = $this->db->queryAll($sql);
        
        $pics = [];
        
        $rand_pics = array_rand($pics_arr, 2);
        $rand_pic1 = $pics_arr[$rand_pics[0]];
        array_push($pics, $rand_pic1);
        $rand_pic2 = $pics_arr[$rand_pics[1]];
        array_push($pics, $rand_pic2);
        
        return $pics;
    }
     
    public function getPics($id) {
        $sql = "SELECT id, nameBook, amount, text, img_path, Themes_id, Types_id, Users_login  FROM Pics WHERE id=:id";
        $params = [
            'id'=>$id
                ];
        $pic_arr = $this->db->execute($sql, $params, true);
        
  foreach ($pic_arr as $pics) { 
        //choose theme
        $id_theme = $pics['Themes_id'];   
        $sql = "SELECT theme FROM Themes WHERE id=:id";
        $params = [
            'id'=>$id_theme
                ];
        $theme_arr = $this->db->execute($sql, $params, true);
        foreach ($theme_arr as $key => $value) {
            $theme = $value["theme"];            
        }
        $pics['Themes_id'] = $theme;
        
        //choose instrument
        $id_type = $pics['Types_id'];   
        $sql_type = "SELECT type FROM Types WHERE id=:id";
        $params_type = [
            'id'=>$id_type
                ];
        $type_arr = $this->db->execute($sql_type, $params_type, true);
        foreach ($type_arr as $key => $value) {
            $type = $value["type"];
        }
         $pics['Types_id'] = $type;
      
        if ($pics["id"] == $id){
            $_SESSION['idPics'] =  $pics['id'];
            return $pics;       
        }  
    }
    
}
  //get pics for different types of instruments
   public function getPicsByType($type) {
          $sql = "SELECT id, nameBook, amount, text, img_path, Themes_id, Types_id, Users_login  FROM Pics WHERE Types_id=:Types_id ORDER BY id ASC";
        $params = [
            'Types_id'=>$type
                ];
        $pics = $this->db->execute($sql, $params, true);
        
            return $pics;       
        }  

 //check size and type
    public function loadPics($data) {

        $iWidth = 860;
        $iHeight = 380;
        $iJpgQuality = 100;

        $pics = $_FILES;
        
        if ($pics) {
      
        $tmp_name = $pics["picture"]["tmp_name"];
        if($tmp_name == ""){
            return self::NO_PIC;
        }
        
        $error = $pics['picture']['error'];
       
        if($error == UPLOAD_ERR_FORM_SIZE){
        return self::SIZE_ERROR;
    } 

    //check types
    $types = array('image/png','image/jpeg', 'image/jpg');
    $finfo = finfo_open(FILEINFO_MIME_TYPE);   
    $type_pic = finfo_file($finfo, $tmp_name);
        
        //choose theme
        if($data["theme"] === "Nature") {
            $themes = 1;    
        }elseif($data["theme"] === "Space"){
            $themes = 2;    
        }elseif($data["theme"] === "Animals"){
            $themes = 3;    
        }elseif($data["theme"] === "Cars"){
            $themes = 4;    
        }elseif($data["theme"] === "Cities"){
            $themes = 5;    
        }else{
            $themes = 6;
        }
            
        //choose type
        if($data["type"] === "pen") {
            $type = 1;    
        }elseif($data["type"] === "pencil"){
            $type = 2;    
        }elseif($data["type"] === "paints"){
            $type = 3;    
        }elseif($data["type"] === "monochrome"){
            $type = 4;    
        }else{
            $type = 5;    
        }
        
        if(!in_array($type_pic, $types)){
        finfo_close($finfo);
        return self::TYPE_ERROR;
    }
    
                    // new unique filename
                    $sTempFileName = 'img/imgUsers/' . time();

                    // move uploaded file into cache folder
                    move_uploaded_file($pics['picture']['tmp_name'], $sTempFileName);

                    // change file permission to 644
                    @chmod($sTempFileName, 0644);

                    if (file_exists($sTempFileName) && filesize($sTempFileName) > 0) {
                        $aSize = getimagesize($sTempFileName); // try to obtain image info
                        if (!$aSize) {
                            @unlink($sTempFileName);
                            return;
                        }

                        // check for image type
                        switch($aSize[2]) {
                            case IMAGETYPE_JPEG:
                                $sExt = '.jpg';

                                // create a new image from file 
                                $vImg = @imagecreatefromjpeg($sTempFileName);
                                break;
                            /*case IMAGETYPE_GIF:
                                $sExt = '.gif';

                                // create a new image from file 
                                $vImg = @imagecreatefromgif($sTempFileName);
                                break;*/
                            case IMAGETYPE_PNG:
                                $sExt = '.png';

                                // create a new image from file 
                                $vImg = @imagecreatefrompng($sTempFileName);
                                break;
                            default:
                                @unlink($sTempFileName);
                                return;
                        }

                        // create a new true color image
                        $vDstImg = @imagecreatetruecolor( $iWidth, $iHeight );

                        // copy and resize part of an image with resampling
                        imagecopyresampled($vDstImg, $vImg, 0, 0, (int)$_POST['x1'], (int)$_POST['y1'], $iWidth, $iHeight, (int)$_POST['w'], (int)$_POST['h']);

                        // define a result image filename
                        $sResultFileName = time() . $sExt;

                        // output image to file
                        imagejpeg($vDstImg, 'img/imgUsers/'.$sResultFileName, $iJpgQuality);
                        @unlink($sTempFileName);
                        
                    }

    
    $login = $_SESSION['login'];
 
    $sql = "INSERT INTO Pics (nameBook, amount, text, `like`, img_path, Themes_id, Types_id, Users_login)
              VALUES (:nameBook, :amount, :text, :like, :img_path, :Themes_id, :Types_id, :Users_login)";
    $params = [
        'nameBook'=>$data['nameBook'],
        'amount'=>$data['amount'],
        'text'=>$data['text'],
        'img_path'=>'/imgUsers/' . $sResultFileName,
        'Themes_id'=>$themes,
        'Types_id'=>$type,
        'Users_login'=>$login,
        'like'=>0
    ];
    
    $statement = $this->db->execute($sql, $params, false);
          return self::LOAD_SUCCESS;
    }
}

        public function loadPicsM($data){
        if ($data == NULL){
        $total = "OK";
        return $total;
        
        }else {
        $iWidth = 860;
        $iHeight = 380;
        $iJpgQuality = 100;

        $pics = $_FILES;
        
        if ($pics) {
        $tmp_name = $pics["picture"]["tmp_name"];
        
        if($tmp_name == ""){
            $total = "NO_PIC";
            return $total;
        }
        
        $error = $pics['picture']['error'];
       
        if($error == UPLOAD_ERR_FORM_SIZE){
            $total = "SIZE_ERROR";
            return $total;        
    } 

    //check types
    $types = array('image/png','image/jpeg', 'image/jpg');
    $finfo = finfo_open(FILEINFO_MIME_TYPE);   
    $type_pic = finfo_file($finfo, $tmp_name);
        
        //choose theme
        if($data["theme"] === "Nature") {
            $themes = 1;    
        }elseif($data["theme"] === "Space"){
            $themes = 2;    
        }elseif($data["theme"] === "Animals"){
            $themes = 3;    
        }elseif($data["theme"] === "Cars"){
            $themes = 4;    
        }elseif($data["theme"] === "Cities"){
            $themes = 5;    
        }else{
            $themes = 6;
        }
            
        //choose type
        if($data["type"] === "pen") {
            $type = 1;    
        }elseif($data["type"] === "pencil"){
            $type = 2;    
        }elseif($data["type"] === "paints"){
            $type = 3;    
        }elseif($data["type"] === "monochrome"){
            $type = 4;    
        }else{
            $type = 5;    
        }
        
        if(!in_array($type_pic, $types)){
        finfo_close($finfo);
        $total = "TYPE_ERROR";
        return $total;                
        }
        
        // new unique filename
        $sTempFileName = 'img/imgUsers/' . time();

        // move uploaded file into cache folder
        move_uploaded_file($pics['picture']['tmp_name'], $sTempFileName);

        // change file permission to 644
        @chmod($sTempFileName, 0644);
            if (file_exists($sTempFileName) && filesize($sTempFileName) > 0) {
                $aSize = getimagesize($sTempFileName); // try to obtain image info
                if (!$aSize) {
                @unlink($sTempFileName);
                return;
                }

        // check for image type
            switch($aSize[2]) {
            case IMAGETYPE_JPEG:
            $sExt = '.jpg';

        // create a new image from file 
            $vImg = @imagecreatefromjpeg($sTempFileName);
            break;
            /*case IMAGETYPE_GIF:
            $sExt = '.gif';
        // create a new image from file 
            $vImg = @imagecreatefromgif($sTempFileName);
            break;*/
            case IMAGETYPE_PNG:
            $sExt = '.png';
        // create a new image from file 
            $vImg = @imagecreatefrompng($sTempFileName);
            break;
            default:
            @unlink($sTempFileName);
            return;
            }
        
        // create a new true color image
            $vDstImg = @imagecreatetruecolor( $iWidth, $iHeight );

        // copy and resize part of an image with resampling
            imagecopyresampled($vDstImg, $vImg, 0, 0, (int)$_POST['x1'], (int)$_POST['y1'], $iWidth, $iHeight, (int)$_POST['w'], (int)$_POST['h']);
        
        // define a result image filename
            $sResultFileName = time() . $sExt;
        
        // output image to file
            imagejpeg($vDstImg, 'img/imgUsers/'.$sResultFileName, $iJpgQuality);
            @unlink($sTempFileName);
            }
    
    //add to DB        
    $login = $_SESSION['login'];
 
    $sql = "INSERT INTO Pics (nameBook, amount, text, `like`, img_path, Themes_id, Types_id, Users_login)
              VALUES (:nameBook, :amount, :text, :like, :img_path, :Themes_id, :Types_id, :Users_login)";
    $params = [
        'nameBook'=>$data['nameBook'],
        'amount'=>$data['amount'],
        'text'=>$data['text'],
        'img_path'=>'/imgUsers/' . $sResultFileName,
        'Themes_id'=>$themes,
        'Types_id'=>$type,
        'Users_login'=>$login,
        'like'=>0
    ];
    
    $statement = $this->db->execute($sql, $params, false);
      $total = 'LOAD_SUCCESS';      
        return $total;
        } 
    }
}   

public function saveComment($comData) {
        
       if(!isset($_SESSION['login'])){
         return self::GO_AUTH;   
        }elseif($comData['comment'] == " "){
            return self::COMMENT_EMPTY;
        } else {
            $login = $_SESSION['login'];
            $idPic = $_SESSION['idPics'];
            $date = date("H:m d/F/Y ");
                        
        $sql = "INSERT INTO Comments (Comment, Date, Users_login, Pics_id)
              VALUES (:Comment, :Date, :Users_login, :Pics_id)";
        $params = [
            'Comment'=>$comData['comment'],
            'Date'=>$date,
            'Users_login'=>$login,
            'Pics_id'=> $idPic
        ];

        $statement = $this->db->execute($sql, $params, false);
        if($statement) {
            return self::DB_ERROR;
        }
               
        return self::COMMENT_SAVED; 
        }
    }

public function addLike() {

        $idPic = $_SESSION['idPic'];
//        
//        if(!isset($_SESSION['login'])){
//         return self::GO_AUTH;   
//        } else {
        
        $sql = "UPDATE Pics SET `like`=:like
              WHERE `id`=:id";
        
        $current = $this->getLikes($idPic);
        
        $params = [
            'like'=>$current+1,
            'id'=>$idPic
        ];

        $statement = $this->db->execute($sql, $params, false);
        if($statement) {
            return self::DB_ERROR;
        }
               
        return self::LIKE; 
//        }  
    }        
   
}




