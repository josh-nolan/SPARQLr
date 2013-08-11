<?php
	require_once('functions/global.php');
	
	if (isset($_GET['id']))
	{
		$id = $_GET['id'];
		$tree_array = getSourceTreeStructure($id);
		$root = getSource($id);
		$nodes = array();
		foreach($tree_array as $node)
		{
			if ($node != "NULL" && $node != "")
			{
				$nodes[$node] = getSource($node);
			}
		}
	}
	else
	{
		header("Location: listSources.php");
	}
	
	$title = "View Source";
	require_once('header.php');
?>
<section id="content">
	<h1>View Source: <?php echo $id;?></h1>
	<h3 id="tree_struc"><?php echo getSourceTreeRaw($id);?></h3>
	<h2>Source Tree</h2>
	<div class="contentBody">
		<div id="is_org" style="width:65%; height:400px; float: left;"></div>
		<div id="nodeInfo" style="width: 30%; height: 400px; float: left; margin-left: 15px;">
			<?php
				foreach($nodes as $n)
				{
					echo "<div id='".$n->title."' class='node inputform'>";
						$w = strpos($n->SPARQL, "WHERE");
						$output = substr($n->SPARQL, 7, $w - 8);
						echo "<h3>$output</h3>";
						echo "ID: <span class='title'>".$n->title."</span><br/>";
						echo "<div class='endpoint'>".$n->endpoint."</div>";
						echo "Materailzed On: <span class='materializedOn'>" . date('d-m-y', $n->dateSaved). "</span><br/>";
						echo "<textarea rows='10' style='width: 100%;'>".$n->SPARQL."</textarea>";
					echo "</div>";
				}
			?>
		</div>
		<div class="clear"></div>
    </div>
    <hr/>
    <h2>Query This Source</h2>
    <p>You can query this source using the editor below, if you wish to save the query then please use the <a href="editor.php">SPARQLr editor</a>.</p>
    <div id="queryholder" style="width: 100%;">
	    <textarea id="querybox" rows="20" cols="100"><?php echo $root->SPARQL; ?></textarea>
	    <div id="queryInfo">
	        <div id="line"></div>
	        <div id="position"></div>
	        <div id="error"></div>
	        <div id="execute">Execute</div>
	    </div>
	</div>
	<h3>Results</h3>
	<div id="results">
	</div>
	<hr/>
</section>
<script>
var query;
	var clearError = function(){};
	var markerHandle = null;
	var previousQuery = null;
	var q = null;
	var fields = ["id", "exposed_value"];
	$(document).ready(function()
	{
		var options = new primitives.orgdiagram.Config();
		options.leavesPlacementType = primitives.orgdiagram.ChildrenPlacementType.Matrix;
		options.hasSelectorCheckbox = primitives.common.Enabled.False;
		options.templates = [getSparqlrTemplate(), getSparqlrParentTemplate()];
		options.onItemRender = onTemplateRender;

		// iterate over tree... hmph
		var tree_struc = $('#tree_struc').html().split(" ");
		var root = new primitives.orgdiagram.ItemConfig();
		var depth = 1;
		var nodes = Array();
		nodes[0] = root;
		$.each(tree_struc, function(key, node)
		{
			if (node == "") return true;
			if (key == 0)
			{
				// root item
				root.title =  $('#' + node).children("h3").html().trim();
				root.endpoint = "";
				root.sparql = "";
				root.exposed_value = "";
				root.templateName = "";
				root.id = node;
				root.URI = node;
				root.templateName = "sparqlrParentTemplate";
				// this will add it to the tree
				options.rootItem = root;
				options.cursorItem = root;
			}
			else
			{
				if (node == "NULL")	depth--;
				else
				{
					// A Child Node
					var child = new primitives.orgdiagram.ItemConfig();
					child.endpoint = "";
					child.sparql = "";
					child.exposed_value = $('#' + node).children("h3").html().trim();
					child.templateName = "";
					child.id = node;
					child.URI = node;
					child.templateName = "sparqlrTemplate";
					// TODO: Make children blue dots!!!
					//child.isVisible = primitives.common.Enabled.False;
					// push child to parent
					nodes[depth - 1].items.push(child);
					nodes[depth] = child;
					depth++;
				}
			}
		});
		
		jQuery("#is_org").orgDiagram(options);

		// highlight code from query box
		var querybox = document.getElementById('querybox');
		query = CodeMirror.fromTextArea(querybox, {
			mode :'sparql_parse',
			lineNumbers: true,
			smartIndent: true,
			matchBrackets: true,
			onChange : validateQuery,
			onCursorActivity : updateQueryInfo
		});
		validateQuery();
		
		$('#execute').click(function()
		{
			executeSparql(query.getValue(), "http://sparqlr.co.uk/endpoint.php", $('#results'));
		});

		$('.bp-item').click(function()
		{
			selectNode($(this).children(".id").first().html());
		});

		$('#execute').click();
		selectNode(root.id);

	});
	
	function selectNode(id)
	{
		$('#nodeInfo .node').hide();
		$('#nodeInfo #' + id).show();
	}
	function onTemplateRender(event, data)
	{
	    var itemConfig = data.context;
	    if (data.templateName == "sparqlrTemplate")
	    {
	        for (var index = 0; index < fields.length; index++) {
	            var field = fields[index];

	            var element = data.element.find("[name=" + field + "]");
	            if (element.text() != itemConfig[field])
	            {
	                element.text(itemConfig[field]);
	            }
	        }
	    }
	    else if (data.templateName == "sparqlrParentTemplate")
	    {
	    	var pFields = ["title", "id"];
	    	for (var index = 0; index < pFields.length; index++)
	    	{
	            var field = pFields[index];
	            var element = data.element.find("[name=" + field + "]");
	            if (element.text() != itemConfig[field])
	            {
	                element.text(itemConfig[field]);
	            }
	        }
	    }
	}

	function getSparqlrTemplate() 
	{
	    var result = new primitives.orgdiagram.TemplateConfig();
	    result.name = "sparqlrTemplate";

	    result.itemSize = new primitives.common.Size(280, 100);
	    var itemTemplate = jQuery(
	        '<div class="bp-item bp-corner-all bt-item-frame">'
	        	//+ '<div name="URI" class="URI"></div>'
	            + '<div name="exposed_value" class="title"></div>'
	            + '<div name="id" class="id" style="display: none;"></div>'
	        + '</div>'
	    ).css({
	        width: result.itemSize.width + "px",
	        height: result.itemSize.height + "px"
	    }).addClass("bp-item bp-corner-all bt-item-frame");


	    result.itemTemplate = itemTemplate.wrap('<div>').parent().html();
	    return result;
	}

	function getSparqlrParentTemplate()
	{
	    var result = new primitives.orgdiagram.TemplateConfig();
	    result.name = "sparqlrParentTemplate";

	    result.itemSize = new primitives.common.Size(280, 100);
	    var itemTemplate = jQuery(
	        '<div class="bp-item bp-corner-all bt-item-frame">'
	            + '<div name="title" class="title"></div>'
	            + '<div name="id" class="id" style="display: none;"></div>'
	        + '</div>'
	    ).css({
	        width: result.itemSize.width + "px",
	        height: result.itemSize.height + "px"
	    }).addClass("bp-item bp-corner-all bt-item-frame");

	    result.itemTemplate = itemTemplate.wrap('<div>').parent().html();
	    return result;
	}

	function validateQuery()
	{
		if (markerHandle !== null) {
			query.clearMarker(markerHandle);
		}
		var state;
		var l;
		for (l = 0; l < query.lineCount(); ++l) 
		{
			state = query.getTokenAt({
				line : l,
				ch : query.getLine(l).length
			}).state;
			if (state.OK === false)
			{
				markerHandle = query.setMarker(l,"<span style=\"color: #f00 ; font-size: large;\">&rarr;</span> %N%");
				clearError = query.markText({
					line : l,
					ch : state.errorStartPos
				}, {
					line : l,
					ch : state.errorEndPos
				}, "sp-error");
				break;
			}
		} // for each token
		updateQueryInfo();
		
		if (state.complete)
		{
			// query valid
			document.getElementById('error').innerHTML = " | <span style='color:green;'>Query valid</span>";
			$('#execute').removeClass('inactive');
		}
		else 
		{
			// query invalid
			document.getElementById('error').innerHTML = " | <span style='color: red;'>Query invalid</span>";
			$('#execute').addClass('inactive');
		}
	};

	function updateQueryInfo()
	{
		document.getElementById('line').innerHTML = "Line: " + query.getCursor().line;
		document.getElementById('position').innerHTML = "Position: " + query.getCursor().ch;
	}
</script>
<?php require_once('footer.php'); ?>