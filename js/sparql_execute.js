// create a uri to execute using using YQL
function createYQLRestQuery(endpoint, query)
{
	// code snippet taken from 
	// http://snipplr.com/view.php?codeview&id=53633
	var yql = "use\"http://triplr.org/sparyql/sparql.xml\" as sparql;";
	yql += "SELECT * FROM sparql WHERE query ='";
	
	// change any ' to "
	query = query.replace(/'/g, "\"");
	yql += query + "'";
    yql += "AND service=\""+ endpoint + "\"";
	
	// now we need to stick this into a URL which when sent will execute on YQL stuff
	var YahooUrl = "http://query.yahooapis.com/v1/public/yql?q=";
	YahooUrl += encodeURIComponent(yql);
	YahooUrl += "&format=json&diagnostics=true";
	console.log(YahooUrl);
	$('#log').html(getTimeString() + "- Query executed using YQL over AJAX - <a href=\"" + YahooUrl.toString()+ "\">link</a>" + "<br/>" +  $('#log').html()); 
	
	$.ajax({ 
   		type: "GET",
   		dataType: "jsonp",
   		url: YahooUrl,
		error:function(){ alert("some error occurred") },
    	success:function(response){ resultsRecieved(response); }
	});
}

function resultsRecieved(data)
{
	console.log(data);
	var failed = false;
	if (data.error)
	{
		$('#log').html(	getTimeString() + "- YQL error: "
					   + data.error.description
					   + "<br/>" +  $('#log').html()); 
		failed = true;
	}
	else if (data.query.results == null)
	{
		$('#log').html(	getTimeString() + "- JS Error: "
					   + data.query.diagnostics.javascript[0].substring(0, data.query.diagnostics.javascript[0].search("url") - 2)
					   + "<br/>" +  $('#log').html()); 
		failed = true;

	}
	else if (typeof data.query.results.sparql.result != 'undefined')
	{		
		var t =  data.query.diagnostics["user-time"]/6000;
		$('#log').html(	getTimeString() + "- results recieved - time:" 
						+ t.toFixed(2)
						+ " rows:" + data.query.results.sparql.result.length  
						+ "<br/>" +  $('#log').html()); 
	}
	else
	{
		// data returned but with 0 rows :(
		var t =  data.query.diagnostics["user-time"]/6000;
		$('#log').html(	getTimeString() + "- results recieved - time:" 
					   	+ t.toFixed(2) 
						+ " rows: 0"  
					   	+ "<br/>" +  $('#log').html()); 
		var failed = true;
	}
	if (!failed)
	{
		var aDataSet = data.query.results.sparql.result,
			columns = new Array(),
			dataArray = new Array(),
		    onColumn = 0,
			onData = 0,
			columnsSet = false;
		
		
		for (var k in aDataSet)
		{ 
			dataArray[onData] = new Array();
			if($.isArray(aDataSet))
			{
				// data set is array so we need to go a level deeper to get results
				var counter = 0;
				for (var i in aDataSet[k])
				{		
					if (!columnsSet)
					{
						columns[onColumn] = {"sTitle" : i};
						onColumn++;
					}
					dataArray[onData][counter] = aDataSet[k][i].value;
					counter++;
				}
				columnsSet = true;
				onData++;
			}
			else
			{
				// data set is just one row so stay here
				columns[onColumn] = {"sTitle" : k};
				onColumn++;
				columnSet = true;
					
				// now fill our data array
				dataArray[onData][0] = aDataSet[k].value;
				onData++;
			}
		}
		
		resultsDiv.text(columns + "<br/><hr/><br/> " + dataArray);
		resultsDiv.html( '<table cellpadding="0" cellspacing="0" border="0" class="display" id="example"></table>' );
		$('#example').dataTable({
			"aaData": dataArray,
			"aoColumns":  columns,
			"fnFilter" : false
			//]
		});

		// set output field
		if(typeof window.setOutput == 'function')
		{
			// function exists, so we can now call it
			setOutput();
		}
	}

}

var resultsDiv = null;
var previousExecutedQuery = null;
// take a query and a data source and execute using YQL -returning data set will be handle in another function
function executeSparql(sparqlQuery, endpoint, results)
{
	sparqlQuery.replace(/(\r\n|\n|\r)/gm," ");
	//sparqlQuery.replace(/\'/, "");
	var qNoSpaces = sparqlQuery.replace(/\s/g, '');
	if (previousExecutedQuery != sparqlQuery)
	{
		console.log(previousExecutedQuery + " : " + qNoSpaces);
		resultsDiv = results;
		createYQLRestQuery(endpoint, sparqlQuery);		
		previousExecutedQuery = qNoSpaces;
	}	
	else
	{
		console.log("Query hasn't changed, no need to execute");	
	}
}

function getTimeString()
{
    var str = "";
    var currentTime = new Date();
    var hours = currentTime.getHours();
    var minutes = currentTime.getMinutes();
    var seconds = currentTime.getSeconds();
    if (minutes < 10) {
        minutes = "0" + minutes
    }
    if (seconds < 10) {
        seconds = "0" + seconds
    }
    str += hours + ":" + minutes + ":" + seconds + " ";
    return str;
}