<?php

class Tools
{
    public static function GetLauncherVersion()
    {
        $jsonObj = new \stdClass();
        $jsonObj->launcher_version = "1.0.7917.32108";

        $file_name = "updates/Nighthold Launcher.exe";

        try {
            $key = "P\x00r\x00o\x00d\x00u\x00c\x00t\x00V\x00e\x00r\x00s\x00i\x00o\x00n\x00\x00\x00";
            $fptr = fopen($file_name, "rb");
            $data = "";

            while (!feof($fptr))
            {
                $data .= fread($fptr, 65536);

                if (strpos($data, $key)!==FALSE)
                    break;

                $data = substr($data, strlen($data)-strlen($key));
            }

            fclose($fptr);
            
            if (strpos($data, $key)===FALSE) {
                return "";
            }

            $pos = strpos($data, $key)+strlen($key);

            $version = "";

            for ($i=$pos; $data[$i]!="\x00"; $i+=2) {
                $version .= $data[$i];
            }

            
            if ($version != "") {
                $jsonObj->launcher_version = $version;
            }
        } catch (Exception $e) {
            // todo: catch exceptions to some logs?
        }
        
        echo json_encode($jsonObj, JSON_PRETTY_PRINT);
    }

    public static function convert_from_latin1_to_utf8_recursively($dat)
    {
        if (is_string($dat))
        {
            return utf8_encode($dat);
        }
        elseif (is_array($dat))
        {
            $ret = array();
            foreach ($dat as $i => $d) $ret[ $i ] = self::convert_from_latin1_to_utf8_recursively($d);
            return $ret;
        }
        elseif (is_object($dat))
        {
            foreach ($dat as $i => $d) $dat->$i = self::convert_from_latin1_to_utf8_recursively($d);
            return $dat;
        }
        else
        {
            return $dat;
        }
    }
    
    public static $GameFilesArray = array();
    
    public static function GetFilesList($expansionID, $dir = "")
    {   
        ob_start();
        
        if (!$dir)
            $dir = "../game/".$expansionID;
        
        $dh = new DirectoryIterator($dir);
        
        $pathArray = array();
        
        foreach ($dh as $item)
        {
            if (!$item->isDot())
            {
                if ($item->isDir())
                    self::GetFilesList($expansionID, "$dir/$item");
                else 
                {
                    $fileName = $item->getFilename();
                    $extension = $item->getExtension();
    
                    if ($extension != "php" && $extension != "htaccess")
                    {
                        $path = $dir ."/".$fileName;

                        $fileSize = self::GetFileSize($path);
                        $modifiedTime = self::GetFileModifiedTime($path);
                        
                        $path = str_replace('../game/'.$expansionID.'/', '', $path);
                        
                        $row_array['filePath'] = $path;
                        $row_array['fileSize'] = $fileSize;
                        $row_array['modifiedTime'] = $modifiedTime;

                        array_push($pathArray, $row_array);
                    }
                }
            }
        }
            
        array_push(self::$GameFilesArray, $pathArray);
        
        ob_end_clean();
        
        echo json_encode(self::$GameFilesArray, JSON_PRETTY_PRINT);
    }
    
    public static function GetFileSize($path)
    {
        if (!file_exists($path))
            return false;
    
        $size = filesize($path);
    
        if (!($file = fopen($path, 'rb')))
            return false;
    
        if ($size >= 0)
        {//Check if it really is a small file (< 2 GB)
            if (fseek($file, 0, SEEK_END) === 0)
            {//It really is a small file
                fclose($file);
                return $size;
            }
        }
    
        //Quickly jump the first 2 GB with fseek. After that fseek is not working on 32 bit php (it uses int internally)
        $size = PHP_INT_MAX - 1;
        if (fseek($file, PHP_INT_MAX - 1) !== 0)
        {
            fclose($file);
            return false;
        }
    
        $length = 1024 * 1024;
        while (!feof($file))
        {//Read the file until end
            $read = fread($file, $length);
            $size = bcadd($size, $length);
        }
        $size = bcsub($size, $length);
        $size = bcadd($size, strlen($read));
    
        fclose($file);
        return $size;
    }
    
    // public static function GetFileSize($file) 
    // {
        // $size = filesize($file);
        // if ($size <= 0)
        // {
            // if (!(strtoupper(substr(PHP_OS, 0, 3)) == 'WIN')) {
                // $size = trim(`stat -c%s $file`);
            // }
            // else{
                // $fsobj = new COM("Scripting.FileSystemObject");
                // $f = $fsobj->GetFile($file);
                // $size = $f->Size;
            // }
        // }
        // return $size;
    // }
    
    public static function GetFileModifiedTime($filename)
    {
        return filemtime($filename);
    }
    
    public static function GetMd5Hash($filePath)
    {
        return md5_file($filePath);
    }  
    
    public static function GetSha1Hash($filePath)
    {
        return sha1_file($filePath);
    }
}
