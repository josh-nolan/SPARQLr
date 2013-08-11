<?php

class SparqlrQuery
{
	public $title;
	public $queryId;
	public $dateSaved;
	public $endpoint;
	public $SPARQL;
	public $description;
	public $classification;
	public $tags;
	public $type;
	public $viewOfQuery;
	public $hasParameters;
	public $parameters;
	public $rules;

	public function __construct()
	{
		// default constructor?
	}

	public function outputTags()
	{
		$s = "";
		if (isset($this->tags))
		{
			foreach($this->tags as $k => $v)
			{
				$s .= "<a href='$siteURL/search.php?type=".$this->type ."&field=Tag&value=".$v."'>".$v."</a>, ";
			}
			return substr($s, 0, -2);
		}
		return "No tags set";
	}

	public function outputModuleHtml()
	{
		echo "<div class=\"query\">";
		echo "<a href=\"q/" .$this->queryId . "\"><h3>" . $this->title . "</h3></a>";
        echo "<p>" . $this->description  . "</p>";
        echo "<hr/>";
        echo "<p class='inputform'>" . $this->endpoint . " | " . $this->classification . "</p>";
        echo "<p class='inputform'>TAGS: ".$this->outputTags()."</p>";
		echo "<a href=\"q/" . $this->queryId  . "\" class=\"button\">View Query</a>";
		echo "</div>";
    }

    public function outputModuleViewHtml()
    {
		echo "<div class=\"query\">";
		echo "<h3>" .$this->title. "</h3>";
		echo "<span class=\"inputform\">Materailzed On: " . date('d-m-y', $this->dateSaved). "</span><br/>";
		echo "<div class=\"buttonHolder\">";
		echo "<a href=\"q/id=" . $this->viewOfQuery . "\" class=\"button first\">SPARQL</a>";
		echo "<a href=\"saveView.php?id=". $this->viewOfQuery ."\" class=\"button middle\">RE-MATERIALIZE</a>";
		echo "<a href=\"viewView.php?id=". $this->title ."\" class=\"button last\" >VIEW</a>";
		echo "</div>";
		echo "</div>";
    }
}
?>