<?php

/*


           -
         /   \
      /         \
   /   MINECRAFT   \
/         PHP         \
|\       CLIENT      /|
|.   \     2     /   .|
| ..     \   /     .. |
|    ..    |    ..    |
|       .. | ..       |
\          |          /
   \       |       /
      \    |    /
         \ | /
         
         
    by @shoghicp



            DO WHAT THE FUCK YOU WANT TO PUBLIC LICENSE
    TERMS AND CONDITIONS FOR COPYING, DISTRIBUTION AND MODIFICATION

    0. You just DO WHAT THE FUCK YOU WANT TO.


*/


//22
// 1.0.0, 1.0.1

$pstruct = array(
    0x00 => array(
        "int",
    ),
    
    0x01 => array(
        "int",
        "string",
        "long",
        "int",
        "byte",
        "byte",
        "ubyte",
        "ubyte",
    ),
    
    0x02 => array(
        "string",    
    ),
    
    0x03 => array(
        "string",
    ),
    
    0x04 => array(
        "long",
    ),    
    
    0x05 => array(
        "int",
        "short",
        "short",
        "short",
    ),
    
    0x06 => array(
        "int",
        "int",
        "int",
    ),
    
    0x08 => array(
        "short",
        "short",
        "float",
    ),
    
    0x09 => array(
        "byte",
        "byte",
        "byte",
        "short",
        "long",
    ),
    
    0x0d => array(
        "double",
        "double",
        "double",
        "double",
        "float",
        "float",
        "bool",
    ),
    
    0x10 => array(
        "short",
    ),
    
    0x11 => array(
        "int",
        "byte",
        "int",
        "byte",
        "int",
    ),
    
    0x12 => array(
        "int",
        "byte",
    ),
    
    0x14 => array(
        "int",
        "string",
        "int",
        "int",
        "int",
        "byte",
        "byte",
        "short",
    ),
    
    0x15 => array(
        "int",
        "short",
        "byte",
        "short",
        "int",
        "int",
        "int",
        "byte",
        "byte",
        "byte",
    ),
    
    0x16 => array(
        "int",
        "int",
    ),
    
    0x17 => array(
        "int",
        "byte",
        "int",
        "int",
        "int",
        "int", //if >0, fireball
        "short",
        "short",
        "short",
    ),
    
    0x18 => array(
        "int",
        "byte",
        "int",
        "int",
        "int",
        "byte",
        "byte",
        "entityMetadata",
    ),
    
    0x19 => array(
        "int",
        "string",
        "int",
        "int",
        "int",
        "int",
    ),
    
    0x1a => array(
        "int",
        "int",
        "int",
        "int",
        "short",
    ),
    
    0x1b => array(
        "float",
        "float",
        "float",
        "float",
        "bool",
        "bool",    
    ),
    
    0x1c => array(
        "int",
        "short",
        "short",
        "short",
    ),
    
    0x1d => array(
        "int",
    ),
    
    0x1e => array(
        "int",
    ),
    
    0x1f => array(
        "int",
        "byte",
        "byte",
        "byte",
    ),
    
    0x20 => array(
        "int",
        "byte",
        "byte",
    ),
    
    0x21 => array(
        "int",
        "byte",
        "byte",
        "byte",
        "byte",
        "byte",
    ),
    
    0x22 => array(
        "int",
        "int",
        "int",
        "int",
        "byte",
        "byte",
    ),
    
    0x26 => array(
        "int",
        "byte",
    ),
    
    0x27 => array(
        "int",
        "int",
    ),
    
    0x28 => array(
        "int",
        "entityMetadata",
    ),
    
    0x29 => array(
        "int",
        "byte",
        "byte",
        "short",
    ),
    
    0x2a => array(
        "int",
        "byte",
    ),
    
    0x2b => array(
        "float",
        "short",
        "short",
    ),
    
    0x32 => array(
        "int",
        "int",
        "bool",
    ),
    
    0x33 => array(
        "int",
        "short",
        "int",
        "byte",
        "byte",
        "byte",
        "int",
        "chunkArray",
    ),
    
    0x34 => array(
        "int",
        "int",
        "short",
        "multiblockArray",
    ),
    
    0x35 => array(
        "int",
        "byte",
        "int",
        "byte",
        "byte",
    ),
    
    0x36 => array(
        "int",
        "short",
        "int",
        "byte",
        "byte",
    ),
    
    0x3c => array(
        "double",
        "double",
        "double",
        "float",
        "int",
        "explosionRecord"
    ),
    
    0x3d => array(
        "int",
        "int",
        "byte",
        "int",
        "int",
    ),
    
    0x46 => array(
        "byte",
        "byte",
    ),
    
    0x47 => array(
        "int",
        "bool",
        "int",
        "int",
        "int",
    ),
    
    0x64 => array(
        "byte",
        "byte",
        "string",
        "byte",
    ),
    
    0x65 => array(
        "byte",
    ),
    
    0x67 => array(
        "byte",
        "short",
        "slotData",
    ),
    
    0x68 => array(
        "byte",
        "short",
        "slotArray",
    ),
    
    0x69 => array(
        "byte",
        "short",
        "short",
    ),
    
    0x6a => array(
        "byte",
        "short",
        "bool",
    ),
    
    0x6b => array(
        "short",
        "slotData",
    ),
    
    0x6c => array(
        "byte",
        
    ),
    
    0x82 => array(
        "int",
        "short",
        "int",
        "string",
        "string",
        "string",
        "string",
    ),
    
    0x83 => array(
        "short",
        "short",
        "ubyte",
        "byteArray",
    ),
    
    0xc8 => array(
        "int",
        "byte",
    ),
    
    0xc9 => array(
        "string",
        "byte",
        "short",
    ),
    
    0xcb => array(
        "string",
    ),
    
    0xfa => array(
        "string",
        "short",
        "byteArray",
    ),
    
    0xfe => array(
        "string",
    ),
    
    0xff => array(
        "string",    
    ),
);


?>