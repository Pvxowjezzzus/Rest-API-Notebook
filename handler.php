<?php
namespace notebook;
use notebook\libs\Db;
use notebook\libs\Images;
   
class Handler 
{
    
    private $errors = [];
    private $db;
    public $query;
    public $filePath;
    public function __construct()
    {
        include('libs/Db.php');
        $this->db = new Db();
        if(!empty($_GET['id']) && !empty($_GET['email'])){

           $photo = $this->db->column("SELECT photo FROM notes WHERE id = ".$_GET['id']."");
           if($photo !== 'null') {
             $path = "content/images/";
             unlink($path.$photo);
           }
           $this->db->query("DELETE FROM notes WHERE id = ".$_GET['id']."");
           header("Location: http://notebook/notes.php"); 
           exit;
        }
        
        if(!empty($_SESSION['id'])){
            $check = $this->db->row('SELECT * FROM notes WHERE id='.$_SESSION["id"].'');
            if(!$check){
                header("Location: http://notebook/"); 
                exit;
            }
        }
    }

    public function checkNote(){
        session_start();
        if(!empty($_POST)) {
                $fio =  mb_convert_case($_POST['fio'], MB_CASE_TITLE, "UTF-8");
                if(!preg_match('/^[а-яА-Яa-zA-Z\s]+(-[а-яА-Яa-zA-Z\s]+)? [а-яА-Яa-zA-Z\s]+( [а-яА-Яa-zA-Z]+)?$/u', $fio)  || strlen($fio) < 7 || strlen($fio) > 100){
                  $this->errors[] = 'fio';
                }
        
                if(!preg_match('/^\s?(\+\s?7|8)([- ()]*\d){10}$/', $_POST['phone'])){
                    $this->errors[] = 'phone';
                }
                if(!preg_match('/([a-zA-Z0-9]+@[a-zA-Z0-9]+\.[a-zA-Z0-9])/',$_POST['email'])) {
                    $this->errors[] = 'email';
                }
                if(!empty($_POST['company']) &&  !preg_match('/[а-яА-Яa-zA-Z0-9]$/u',$_POST['company'])) {
                    $this->errors[] = 'company';
                }
                if(!isset($_SESSION['id'])){
                    unset($_SESSION['photo']);
                }
                $this->uploadImage();
                if(!empty($this->errors)){
                    return $this->message($this->errors, http_response_code(400), 'Some Errors');
                }

                if(!isset($_SESSION['id']) && empty($_SESSION['id']) && empty($this->errors)) {
                    
                    $params = [
                        'id' => null,
                        'fio' => $fio,
                        'company' => $this->pure($this->checkEmpty($_POST['company']),ENT_NOQUOTES),
                        'phone' => $this->pure($_POST['phone'],ENT_NOQUOTES),
                        'email'=> $this->pure($_POST['email'],ENT_NOQUOTES),
                        'birth_date'=> $this->pure($this->checkEmpty($_POST['date']),ENT_NOQUOTES),
                        'photo'=> $this->filePath,
                        'created_at'=>date("Y-m-d H:i:s")
                    ];
                    $this->db->query('INSERT INTO notes (`id`, `fio`, `company`, `phone`, `email`,`birth_date`,`photo`,`created_at`)
                                    VALUES (:id, :fio, :company, :phone, :email, :birth_date, :photo, :created_at)', $params);
                                     
                    $this->message('',http_response_code(200),'Запись добавлена'); 
                }

                
        
        }  
    }
        
        public function showNotes(){
                $this->query =  $this->db->row('SELECT * FROM notes');
                return $this->query;
        }
    public function showNote(){
        $this->query =  $this->db->row('SELECT * FROM notes WHERE id='.$_GET["id"].'');
        if(!$this->query){
            header("Location: http://notebook/notes.php"); 
            exit;
        }
        return $this->query;
    }
    public function editNote(){
       
        session_start();
        $this->checkNote();
        $params = [
            'id'=> $_SESSION['id'],
            'fio' =>  $this->pure($this->checkEmpty($_POST['fio']), ENT_NOQUOTES),
            'company' => $this->pure($this->checkEmpty($_POST['company']), ENT_NOQUOTES),
            'phone' => $this->pure($_POST['phone'], ENT_NOQUOTES),
            'email'=> $this->pure($_POST['email'], ENT_NOQUOTES),
            'birth_date'=> $this->pure($this->checkEmpty($_POST['date']), ENT_NOQUOTES),
            'photo'=> $_SESSION['photo'],
        ];

            $this->query = $this->db->column('SELECT id FROM notes WHERE id = :id AND fio = :fio AND company = :company AND phone = :phone 
            AND email = :email AND birth_date = :birth_date AND photo = :photo', $params);  
            if($this->query) {
                $this->message('null',http_response_code(200),'Изменений нет!'); 
            }

            else {
                $params = [
                    'id'=> $_SESSION['id'],
                    'fio' =>  $this->pure($this->checkEmpty($_POST['fio']), ENT_NOQUOTES),
                    'company' => $this->pure($this->checkEmpty($_POST['company']), ENT_NOQUOTES),
                    'phone' => $this->pure($_POST['phone'], ENT_NOQUOTES),
                    'email'=> $this->pure($_POST['email'], ENT_NOQUOTES),
                    'birth_date'=> $this->pure($this->checkEmpty($_POST['date']), ENT_NOQUOTES),
                    'photo'=> $this->pure($this->filePath, ENT_NOQUOTES),
                ];
        
                 $this->db->query('UPDATE notes SET id = :id, fio = :fio, company = :company, phone = :phone,
                 email = :email, birth_date= :birth_date, photo = :photo WHERE id = "'.$_SESSION['id'].'"' , $params);
                 $this->message($_SESSION['id'],http_response_code(200),'Данные записи изменены!');
                 unset ($_SESSION['id']);
                 unset($_SESSION['photo']);
            }
           

    }

    
    public function getNoteData(){
        session_start();
         if(!empty($_SESSION['id']) && isset($_SESSION['id'])){
            $this->query = $this->db->row("SELECT * FROM notes WHERE id = '".$_SESSION['id']."'");
            return $this->query; 
         }
    }
    public function message($object, $status, $message)
	{
		exit(json_encode(['object'=>$object, 'status' => $status, 'message'=> $message]));
    }

    public function checkEmpty($str){
        if(!empty($str))
            return $str;
        else 
            return 'null';
    }
    public function pure($str, $flags)
    {
        return trim(htmlentities(strip_tags($str), $flags, "UTF-8"));
    }

    public function uploadImage(){
        $query = $this->db->column("SELECT photo FROM notes WHERE id = '".$_SESSION['id']."'");
        if($query !== 'null' && !empty($query)){

            $this->filePath = $query;
        }
        else {
            $this->filePath = 'null';
        }
            $allowed_types = [IMAGETYPE_PNG, IMAGETYPE_JPEG, IMAGETYPE_JPEG2000, IMAGETYPE_BMP, IMAGETYPE_GIF, IMAGETYPE_JPX];
            if($_FILES['photo']['size'] > 0 ) {
            if (!in_array(exif_imagetype($_FILES['photo']['tmp_name']),$allowed_types)) {
                $this->errors[] = 'photo';
                }  
                else {
                    
                    $tmpname = $_FILES['photo']['tmp_name'];
                    $uploadImage = basename($_FILES['photo']['name']);
        
                    $path = "content/images/";
                    static $randStr = '0123456789abcdefghijklmnopqrstuvwxyz';
                    $randname = '';
                    for ($i = 0; $i < 10; $i++) {
                        $key = rand(0, strlen($randStr) - 1);
                        $randname .= $randStr[$key];
                    }
                
                    $uploadImageName = trim(strip_tags($uploadImage));
                    $path_info = pathinfo($uploadImageName);
                    $extension =  $path_info['extension'];
                    $file = $randname . '.' . $extension;
                    if (!move_uploaded_file($tmpname, $path.$file)) {
                    $this->error = 'Ошибка загрузки изображения';
                    }
                    else {
                        $size = GetImageSize($path.$file);
                        $this->filePath = $file;
                        if(isset($_SESSION['photo']) && !empty($_SESSION['photo']) && $_SESSION['photo'] !== 'null') { 
                        unlink($path.$_SESSION['photo']); 
                        }
                        
                    }
                    $_SESSION['photo'] = $file;
                    if ($size[0] > 1070 || $size[1] > 540) {
                        include('libs/Images.php');
                        $image = new Images();
                        $image->load($path.$file);
                        $image->resize(1070, 540);
                        $image->save($path.$file);

                    }


            }

    }
    }
}


if(isset($_POST) && !empty($_POST)){
    $function = new Handler();
    if($_POST['actionFunction'] === "checkNote"){
        $function->checkNote();
    }
    else {
         $function->{$_POST['actionFunction']}();
    }
       
}

?>