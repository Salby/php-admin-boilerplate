<?php
/**
 * Created by PhpStorm.
 * User: sander
 * Date: 7/28/18
 * Time: 3:02 PM
 */

require_once('incl/init.php');
?>


<div>
    <input type="text" name="q" placeholder="search">
</div>

<div id="search-result">
    <h1>Search</h1>
</div>

<script src="cms/assets/script.js"></script>
<script>
    new Search({
      url: 'test.php',
      method: 'GET',
      container: 'search-result'
    });
</script>