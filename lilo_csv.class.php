<?php


class LiloCsv implements Iterator{
    
    protected $internalIterator;
    protected $csvSeparator;
    protected $fileHandle;
    protected $current;
    protected $title;

    public function __construct() {
        $this->internalIterator = 0;
        $this->csvSeparator = ';';
        $this->fileHandle = null;
        $this->current = null;
        $this->title = false;
    }
    
    public function setSeparator($separator){
        $this->csvSeparator = $separator;
    }
    
    public function load($file, $title = false){
        
        if($this->fileHandle !== null){
            throw new Exception('A file is already loaded.');
        
            return false;
        }
        
        if(!file_exists($file)){
            throw new Exception('File don\'t exists.');
            
            return false;
        }
        
        $handle = fopen($file, 'r');
        
        if(!$handle){
            throw new Exception('Fopen fail to open the file.');
            
            return false;
        }
        
        $this->fileHandle = $handle;
        
        $this->activateTitle = $title;
    }
    
    public function current(){
        if(is_null($this->current)){
            throw new Exception('nothing in current item, check initialization');
            
            return false;
        }
        
        if($this->title !== false && is_array($this->title)){
            
            foreach($this->current as $key => $item){
                $this->current[$this->title[$key]] = $item;
            }
        }
        
        return $this->current;
    }
    
    public function key(){
        return $this->internalIterator;
    }
    
    public function next(){
        $this->internalIterator++;
    }
    
    public function rewind(){
        rewind($this->fileHandle);
        $this->internalIterator = 0;
        
        //make title
        
        if($this->activateTitle){
            if($this->valid()){
                $current = $this->current();
                
                foreach($current as $key => $item){
                    $this->title[$key] = $item;
                }
            }
        }
    }
    
    public function valid(){
        $current = fgetcsv($this->fileHandle, 0, $this->csvSeparator);
        
        if($current === null){
            throw new Exception('file handle not valid.');
            
            return false;
        }
        
        if($current === false){
            return false;
        }
        
        $this->current = $current;
        
        return true;
    }
    
}

//chargement de la classe :
$csvFile = new LiloCsv();

//chargement du fichier :
$csvFile->load('file.csv');

//on peux foreach dessus, le parcours se fait ligne par ligne et renvoi un tableau contenant les colonnes du fichier (tableau indexé de 0 à n —n étant le nombre de colonne moins 1—)
foreach($csvFile as $item){
    echo $item[1].'<br />'; //je récupère le contenu de la deuxième colonne
}

//ASTUCE : si votre première ligne contient le nom des colonnes de votre fichier CSV, une option permet de les prendre en compte :
$csvFile->load('file.csv', TRUE); //présence du true qui active l'option

//on peux foreach avec le nom des colonnes par exemple dans mon cas j'ai une colonne nommée 'titre' qui est présente
foreach($csvFile as $item){
    echo $item['titre]'].'<br />'; //je récupère le contenu de la deuxième colonne
}
