<?php

    
    if($op == 'index'){
        include xw_template('index');
        
        var_dump(mobile_url('index/index2',['qq'=>1,'ww'=>333]));
    }

    
    if($op == 'index2'){
        include xw_template('index2');
    }
    
?>
