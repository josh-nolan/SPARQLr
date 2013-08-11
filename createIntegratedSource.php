<?php
	require_once('functions/global.php');
	
	$title = "Create an Integrated Source";
	require_once('header.php');
?>
	<section id="content">
		<h1>Create an Integrated Source</h1>
		<div class="contentBody">
			<div id="is_org" style="width:100%; height:600px;"></div>
			 <div id="editLeaf">
		        <div class="form inputform" style="width: 47%; float: left;">
		        	<h2>Edit node</h2>
			        <table width="100%">
			        	<tr>
			        		<td width="100px">Index:</td>
			        		<td><input type="text" style="width: 140px;" name="id" disabled="disabled"/></td>
			        	</tr>
			        	<tr>
			        		<td>Endpoint:</td>
			        		<td><input type="text" name="endpoint" style="width: 300px;" /></td>
			        	</tr>
			        	<tr>
			        		<td colspan="2">SPARQL:</td>
			        	</tr>
			        	<tr>
			        		<td colspan="2">
        			            <div id="queryholder" style="width: 580px;">
					                <textarea id="querybox" rows="20" cols="100"></textarea>
					                <div id="queryInfo">
					                    <div id="line"></div>
					                    <div id="position"></div>
					                    <div id="error"></div>
					                    <div id="execute">Execute</div>
					                </div>
					            </div>
					            <br/>
			        		</td>
			        	</tr>
			        	<tr>
			        		<td>Output:</td>
			        		<td><input type="text" name="exposed_value" disabled="disabled" style="width: 300px;" /></td>
			        	</tr>
			        	<tr>
			        		<td colspan="2">
			        			<div id="log"></div>
			        		</td>
			        	</tr>
			        	<tr>	
			        		<td colspan="2" align="left">
			        			<div class="button" id="saveLeaf">Save</div>
			        		</td>
			        	</tr>
			        </table>				     
		    	</div>
		    	<div id="Sources">
		    		<h3>Datasources</h3>
		    		<div id="Holder">
		    			<?php 
						$sources = getQueries("View");
						foreach($sources as $s)
						{
							echo "<div id='".$s->title."' class='source'>";
								echo "<div classs='title'>".$s->title."</div>";
								//$schema = getQuerySchema($s->title);
								echo "<textarea id='sparql' style='display: none;'>".$s->SPARQL."</textarea>";
								echo "<div id='endpoint' style='display: none;'>".$s->endpoint."</div>";
							echo "</div>";
						}
						$sources = getQueries("Query");
						foreach($sources as $s)
						{
							echo "<div id='".$s->title."' class='source' style='background: #7FAF1B;'>";
								echo "<div classs='title'>".$s->title."</div>";
								//$schema = getQuerySchema($s->title);
								echo "<textarea id='sparql' style='display: none;'>".$s->SPARQL."</textarea>";
								echo "<div id='endpoint' style='display: none;'>".$s->endpoint."</div>";
							echo "</div>";
						}
						?>
						<div class="clear"></div>
		    		</div>
		    		<div id="ChildData" style="display: none;">
		    		</div>
		    	</div>
		    </div>
		    <div class="clear"></div>
		    <div id="Loading" style="width: 100%; height: 100px; display: none;"></div>
		    <hr/>
		    <div id="results" style="width: 100%"></div>
		    <br/>
		    <hr/>
		    <div id="SaveSource">
		    	<h2>Save This Source</h2>
		    	<p>Once you are all done, hit the button below:</p>
		    	<div id="saveTree" class="button">SAVE THE TREES</div>
		    </div>
        </div>
	</section>
<script>
	var query;
	var clearError = function(){};
	var markerHandle = null;
	var previousQuery = null;
	var q = null;
	var fields = ["id", "exposed_value", "endpoint", "sparql", "sparql_s", "m_sparql"];
	$(document).ready(function()
	{
		var options = new primitives.orgdiagram.Config();
		var rootItem = new primitives.orgdiagram.ItemConfig();
		rootItem.title = "Integrated Source";
		rootItem.endpoint = "http://sparqlr.co.uk/endpoint.php";
		rootItem.sparql = "SELECT * WHERE { ?s ?p ?o }";
		rootItem.exposed_value = "Add children to create schema. ..";
		rootItem.templateName = "sparqlrParentTemplate";
		rootItem.id = uniqueId();
		rootItem.URI = rootItem.id;

		options.onMouseClick = onMouseClick;
		options.leavesPlacementType = primitives.orgdiagram.ChildrenPlacementType.Matrix;
		options.hasSelectorCheckbox = primitives.common.Enabled.False;
		options.rootItem = rootItem;
		options.cursorItem = rootItem;
		options.templates = [getSparqlrTemplate(), getSparqlrParentTemplate()];
		options.onItemRender = onTemplateRender;
		jQuery("#is_org").orgDiagram(options);

		$('#saveLeaf').click(function(){
			$('#editLeaf').hide();
			$('#results').hide();
			$('#Loading').show();
			window.location.hash = '#Loading';

			// we need to put the leaf back into the tree
		    var id = $('#editLeaf [name="id"]').val();
		    if (id.length > 1)
		    {
		        $.each($('#is_org').orgDiagram().data()['orgDiagram']['_treeItems'], function(i, value)
		        {   
		            if(value['itemConfig']['id'] == id)
		            {
		                for (var index = 0; index < fields.length; index++)
		                {
		                    var field = fields[index];
		                    var editVal = $('#editLeaf').find("[name=" + field + "]");
		                    
		                     var treeVal = value['itemConfig'][field];
		                     if (treeVal != editVal) {
		                        value['itemConfig'][field] = editVal.val();
		                    }
		                }
		                // we need to save the sparql differently
		                value['itemConfig']['sparql'] = query.getValue();
		               	value['itemConfig']['sparql_s'] = query.getValue().substring(0, 39) + "...";
		                
		                //  'materialise' the query so we get the correct sparql back - and its parents can get data
						getMaterialisedQuerySPARQL();
		                // we have changed stuff so make sure we update the tree :)
		        		updateTree();
		                $("#is_org").orgDiagram("update", primitives.orgdiagram.UpdateMode.Refresh);
		            }
		        });
		    }
			$('#Loading').hide();
		    $('#results').hide();
		    $('#log').html("");
		    window.location.hash = '#is_org';
		});
		
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
			$('#results').show();
			executeSparql(query.getValue(), $('#editLeaf [name="endpoint"]').val(), $('#results'));
			
		});


		$('#editLeaf').hide();
		$('#results').hide();

		$('.source').click( function()
		{
			query.setValue($(this).children('#sparql').val());
			$('#editLeaf [name="endpoint"]').val($(this).children('#endpoint').html());
		});

		$('#saveTree').click( function()
		{
			tree_struc = "";
			preorder_tree($('#is_org').orgDiagram().data()['orgDiagram']['_treeItems'][0]);
			var i = $('#is_org').orgDiagram().data()['orgDiagram']['_treeItems'][0]['itemConfig']['URI'];

			// save the tree structure
			var r = $.ajax({
				url: "saveIntegreatedSourceQuery.php", 
				type: "POST",
				async: false,
				cache: false,
	        	timeout: 30000,
	        	data: {"id": i, "tree_struc": tree_struc}, 
	        	dataType: 'text'
			}).responseText;
			if (r != "1")
			{
				alert ("SAVE FAILED :(");
			}
			else
			{
				window.location = "viewSource.php?id=" + i;
			}

		});
	});


	// functions
	function getMaterialisedQuerySPARQL()
	{
		var i = $('#editLeaf [name="id"]').val();
		var e = $('#editLeaf [name="endpoint"]').val();
		var q = query.getValue();
		// AJAX call passing in query and returning the materialised SPARQL query
		$.ajax({
			url: "saveIntegreatedSourceQuery.php", 
			type: "POST",
			async: true,
			cache: false,
        	timeout: 30000,
        	data: {"id": i, "endpoint": e, "query": q}, 
        	dataType: 'text',
        	success: function(data){
        		console.log("Materialised query successfully for id: " + i);
        		// lets assign the query to the correct node
        		$.each($('#is_org').orgDiagram().data()['orgDiagram']['_treeItems'], function(k, value)
		        {  
		         	if(value['itemConfig']['id'] == i)
		            {
		            	value['itemConfig']['m_sparql'] = data;
		            }
		        });
        	} ,
        	error: function(data)
        	{
        		console.log("Error in materialization for node : " + i);
        		// lets assign the query to the correct node
        		$.each($('#is_org').orgDiagram().data()['orgDiagram']['_treeItems'], function(k, value)
		        {  
		         	if(value['itemConfig']['id'] == i)
		            {
		            	value['itemConfig']['m_sparql'] = "There was an error in materializing " + i + ", trying saving again?";
		            }
		        });
        	}
		});
	}

	function setOutput()
	{
		$('#editLeaf [name="exposed_value"]').val("");
		if ($("#results").html().length > 50)
		{
			$('#results th').each(function(){
				$('#editLeaf [name="exposed_value"]').val($('#editLeaf [name="exposed_value"]').val() +
														 $(this).html() + " ");
			});
		}
	}

	var tree_struc = "";
	function preorder_tree(node)
	{
  		tree_struc += node['itemConfig']['URI'] + " ";

  		if (node['visualChildren'].length > 0)
  		{
  			$.each(node['visualChildren'], function(key, child){
  				preorder_tree(child);
  			});
  			tree_struc += "NULL ";
  		}
		else
		{
			tree_struc += "NULL ";
		}
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
	    	var pFields = ["title", "exposed_value", "URI", "endpoint","sparql"];
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

	    result.itemSize = new primitives.common.Size(320, 150);
	    var itemTemplate = jQuery(
	        '<div class="bp-item bp-corner-all bt-item-frame">'
	        	//+ '<div name="URI" class="URI"></div>'
	            + '<div name="exposed_value" class="title"></div>'
	            + '<div name="endpoint" id="endpoint" class="endpoint"></div>'
	            + '<div name="sparql_s" class="sparql"></div>'
	        + '</div>'
	    ).css({
	        width: result.itemSize.width + "px",
	        height: result.itemSize.height + "px"
	    }).addClass("bp-item bp-corner-all bt-item-frame");

	    var buttons = jQuery(
	        '<div class="buttons">'
	            + '<button class="btn" data-name="edit">Edit</button>'
	            + '<button class="btn" data-name="add">Add</button>'
	            + '<button class="btn" data-name="remove">Remove</button>'
	        + '</div>'
	    );
	    itemTemplate.append(buttons);


	    result.itemTemplate = itemTemplate.wrap('<div>').parent().html();
	    return result;
	}

	function getSparqlrParentTemplate()
	{
	    var result = new primitives.orgdiagram.TemplateConfig();
	    result.name = "sparqlrParentTemplate";

	    result.itemSize = new primitives.common.Size(420, 120);
	    var itemTemplate = jQuery(
	        '<div class="bp-item bp-corner-all bt-item-frame">'
	            + '<div name="title" class="title"></div>'
	            + '<div name="sparql" class="sparql"></div>'
	            + '<div name="exposed_value" class="exposed_value"></div>'
	        + '</div>'
	    ).css({
	        width: result.itemSize.width + "px",
	        height: result.itemSize.height + "px"
	    }).addClass("bp-item bp-corner-all bt-item-frame");

	    var buttons = jQuery(
	        '<div class="buttons">'
	        	+ '<button class="btn" data-name="edit">Edit</button>'
	            + '<button class="btn" data-name="add">Add</button>'
	        + '</div>'
	    );
	    itemTemplate.append(buttons);
	    result.itemTemplate = itemTemplate.wrap('<div>').parent().html();
	    return result;
	}


	function onMouseClick(event, data)
	{
	    var target = jQuery(event.originalEvent.target);
	    if (target.hasClass("btn") || target.parent(".btn").length > 0)
	    {
	        var button = target.hasClass("btn") ? target : target.parent(".btn");
	        var buttonname = button.data("name");

	        switch(buttonname)
	        {
	            case "remove":
	                if( data.parentItem == null )
	                {   
	                    alert("You are trying to delete root item!");
	                }
	                else
	                { 
	                    var position = primitives.common.indexOf(data.parentItem.items, data.context);
	                    data.parentItem.items.splice(position, 1);
	                    // TODO: AJAX Call to delete the triples and remove the file...

	                }
	                break;
	            case "add":
	                var child = new primitives.orgdiagram.ItemConfig();
	                child.exposed_value = "entity";
	                child.endpoint = "http://sparqlr.co.uk/endpoint.php";
	                child.sparql = "SELECT * WHERE { ?s ?p ?o } LIMIT 10";
	                child.sparql_s = child.sparql;
	                child.templateName = "sparqlrTemplate";
	                child.m_sparql = "";
	                child.id = uniqueId();
	                child.URI = child.id;
					
	                //child.URI =  data.context.URI + "/" + child.id;
	                data.context.items.push(child);
	            break;
	            case "edit":
	                editItem(data);
	            break;
	        }
	        data.cancel = true;
	    }

	    updateTree();
	    $("#is_org").orgDiagram("update", primitives.orgdiagram.UpdateMode.Refresh);
	    updateTree();
	    $("#is_org").orgDiagram("update", primitives.orgdiagram.UpdateMode.Refresh);
	}
	function uniqueId()
	{
		var s = (new Date()).getTime().toString();
		return s.substring(7);
	}
	function updateTree()
	{
		// we need to update the root node with the its children entities
		var parent = $('#is_org').orgDiagram().data()['orgDiagram']['_treeItems'][0];
		$('#is_org').orgDiagram().data()['orgDiagram']['_treeItems'][0]['itemConfig']['exposed_value'] = "";

		$.each(parent.logicalChildren, function(i, value)
        {   
        	$('#is_org').orgDiagram().data()['orgDiagram']['_treeItems'][0]['itemConfig']['exposed_value']  += value['itemConfig']['exposed_value'] + " | ";  
        });
	}
	function editItem(d)
	{  
	   	$('#editLeaf').show();
	   	window.location.hash = '#editLeaf';
		for (var index = 0; index < fields.length; index++)
	    {
	        var field = fields[index];
	        var element = $('#editLeaf').find("[name=" + field + "]");
	        if (typeof element != undefined){
	            element.val(d.context[field]);
	        }
	    }
	    // gotta set querybox seperately
	    query.setValue(d.context['sparql']);

		if (d['context']['items'].length > 0)
		{
			$('#Sources #Holder').hide();
			$('#Sources #ChildData').show();
			$('#Sources #ChildData').html("");
			// now lets popluate with the data that is availble (eg. its children)
			$.each(d['context']['items'], function (key, node)
			{
				var columns = node['exposed_value'].split(" ");
				
				var s = "<div class='kid'>"
					s+= "<h3>" + node['URI'] + "</h3>";
					s+= "<textarea rows='7'>";
						s+= node['m_sparql'];
					s+= "</textarea>";
				s += "</div>";
				
				$('#Sources #ChildData').append(s);
				// disable endpoint as it HAS to be from SPARQLr
			});
			$('#editLeaf [name="endpoint"]').val("http://sparqlr.co.uk/endpoint.php");
			$('#editLeaf [name="endpoint"]').attr("disabled", "disabled");
			$('#Sources #ChildData').show();
		}
		else
		{
			$('#editLeaf [name="endpoint"]').removeAttr("disabled");
			$('#Sources #Holder').show();
			$('#Sources #ChildData').hide();
		}
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