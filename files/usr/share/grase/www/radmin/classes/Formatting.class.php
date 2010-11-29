<?

/* Copyright 2008 Timothy White */

class Formatting
{
    public function formatBytes($bytes=0)
    {
        $kb = 1024;
        $mb = $kb*1024;
        $gb = $mb*1024;
        
        $bytes = $bytes + 0;

        if ($bytes >= $gb)
        {
            $output = sprintf ("%01.2f",$bytes/$gb) . " GB";
        }elseif ($bytes >= $mb)
        {
            $output = sprintf ("%01.2f",$bytes/$mb) . " MB";
        }
        elseif ( $bytes >= $kb )
        {
            $output = sprintf ("%01.0f",$bytes/1024) . " Kb";
        }
        elseif ($bytes == 1 )
        {
            $output = $bytes . " byte";        
        }
        else
        {
            $output = $bytes . " bytes";
        }
     
        return $output;
    }


    public function formatSec($seconds = 0)
    {
	    $minutes = intval($seconds / 60 % 60);
	    $hours = intval($seconds / 3600 % 24);
	    $days = intval($seconds / 86400);
	    $seconds = intval($seconds % 60);
	    if($days < 1) return sprintf("%02d:%02d:%02d", $hours, $minutes, $seconds);
	    if($days == 1) return sprintf("%d day %02d:%02d:%02d", $days, $hours, $minutes, $seconds);
	    return sprintf("%d days %02d:%02d:%02d", $days, $hours, $minutes, $seconds);
    }
}

// Formatting::formatBytes(1024*1024*2.56*1024);

?>
