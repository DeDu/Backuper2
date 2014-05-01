<?php
/**
 * Description
 *
 * @author   MD
 * @since    1.0.0
 * @date     19.03.14
 */

class LZ4 {
    public static function compress($path) {
        $compressedfilepath = $path . ".lz4";

        $cmd = "lz4 $path $compressedfilepath  > /dev/null 2>&1";
        exec($cmd, $output, $return);
        unset($output);

        if ($return !== 0) {
            throw new LZ4Exception("Compression Failed. LZ4 Error Code: $return");
        }
        return true;
    }
} 