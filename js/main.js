

function getQueryParameters(query)
{
	var params = new Array();
	var index = 0;
	var n = 0;
	while (index != -1)
	{
		index = query.indexOf("<%", index);
		if (index != -1) 
		{
			params[n] = query.substring(index, query.indexOf(">", index) + 1);
			index++;
			n++;
		}
	}
	if (n == 0) return null;
	return params;
	
}

function validateQuery()
{
	$('#saveArea').hide();
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
		$('#save').addClass('nosave');
		$('#execute').addClass('inactive');
	}
};

function updateQueryInfo()
{
	document.getElementById('line').innerHTML = "Line: " + query.getCursor().line;
	document.getElementById('position').innerHTML = "Position: " + query.getCursor().ch;
}