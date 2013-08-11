<?php 
$pages = Array();
$pages['index.php'] = "Home";
$pages['about.php'] = "About";
$pages['editor.php'] = "Editor";
$pages['listQueries.php'] = "Queries";
$pages['listViews.php'] = "Views";
$pages['listSources.php'] = "Sources";
?>
    <div id="headerBlockAbove"></div>
    <header>
        <div id="logo">
			<a href="index.php"><img src="<?php echo $siteURL;?>/img/logo.png" width="350" alt="SPARQLr | Logo designed by Sophie Nolan" title="SPARQLr | Logo designed by Sophie Nolan" /></a>
        </div>
        <nav>
            <ul>
                <?php
                foreach ($pages as $url => $page)
                {
					echo "<li><a href=\"$siteURL/$url\"";
                    if (basename($_SERVER['PHP_SELF']) == $url) echo "class =\"currentPage\"";
                    echo ">$page ".$_SERVER['___FILE___']."</a></li>";
                }

				// blog link
				echo "<li><a href=\"http://sparqlr.co.uk/blog\">Blog</a></li>";
				// search link
				echo "<li><a href='search.php' class='searchnav'";
                if (basename($_SERVER['PHP_SELF']) == "search.php") echo "class =\"currentPage\"";
                echo ">.</a></li>";
                ?>
            </ul>
        </nav>
        <div class="clearfix"></div>
    </header>  