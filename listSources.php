<?php
  require_once('functions/global.php');
  $title = "List Sources";
  require_once('header.php');
?>
<section id="content">
  <div class="contentBody">
    <h1>Integrated Sources</h1>
    <p>Below are the Integrated Sources that have been created by users. Create a new one <a href="createIntegratedSource.php">here</a>.</p>
 		<div class="queries">
      <?php
        $sources = getSources();
        foreach ($sources as $s_name => $s_value)
        {

          echo "<div class=\"query\">";
            echo "<h3>" . $s_name . "</h3>";
            //echo "<span class=\"inputform\">Materailzed On: " . date('d-m-y', $this->dateSaved). "</span><br/>";
            echo "<p>This source contains a total of ". $s_value ." sources.</p>";
            if (doesTreeHaveCorrectLeaves($s_name))
            {
              echo "<p>THIS TREE HAS ALL OF ITS LEAVES :)</p>";
            }
            else
            {
              echo "<p>THIS TREE HAS LOST SOME LEAVES :(</p>";
            }
            echo "<div class=\"buttonHolder\">";
              echo "<a href=\"viewSource.php?id=" . str_replace(".src", "", $s_name) . "\" class=\"button\">View Source</a>";
            echo "</div>";
          echo "</div>";
        }
      ?> 
      <div class="clear"></div>
    </div>
  </div>
</section>
<?php 
	require_once('footer.php');
?>