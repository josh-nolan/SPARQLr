<?php
  	require_once('functions/global.php');
	$title = "Query Editor";
	require_once('header.php');
    $endpoints = getEndpoints();?>
<section id="content">
    <div class="contentBody">
        <h1>Query Editor</h1>
        <p>SPARQL tutorials as well as guides to linked data can be found <a href="http://linkeddata.org/guides-and-tutorials" target="_blank">here</a>.</p>
		<p><b>* NEW * </b>Add parameters to your query using the following tag <i>&lt;%parameter_name&gt;</i></p>
		<p>Saving of queries has been disabled, please do not try and save your query.</p>
		<div class="column">
            <label for="savedendpoints" class="inputform">SAVED ENDPOINTS:</label>		
            <select name="savedendpoints" id="savedendpoints" class="inputform" style="width: 74%;" >
				<?php
                echo "<option value='' selected></option>";
				foreach ($endpoints as $endpoint => $class)
				{
					echo "<option value='".$endpoint."'>".$endpoint."</option>";
				}
				?>
			</select>
			<br/>
            <label for="endpoint" class="inputform">ENDPOINT:</label>
            <input type="text" name="endpoint" id="endpoint" class="inputform" style="width:84%;"/>

            <div id="queryholder">
                <textarea id="querybox" rows="20" cols="100">
PREFIX dbr: <http://dbpedia.org/resource/>
PREFIX dbp: <http://dbpedia.org/property/>
PREFIX dcterms: <http://purl.org/dc/terms/>
PREFIX foaf: <http://xmlns.com/foaf/0.1/>
PREFIX rdf: <http://www.w3.org/1999/02/22-rdf-syntax-ns#>
PREFIX rdfs: <http://www.w3.org/2000/01/rdf-schema#>
SELECT *
WHERE
{
  ?s ?p ?o
}
LIMIT 10</textarea>
                <div id="queryInfo">
                    <div id="line"></div>
                    <div id="position"></div>
                    <div id="error"></div>
                    <div id="save" class="nosave">Save Query</div> |
                    <div id="execute">Execute</div>
                </div>
            </div>
             <div id="log"></div>
			 <div id="parameters"></div>
        </div>
        <div class="column">
            <div id="results"></div>   
        </div>
        <div class="clear"></div>
        <hr/>
        <form method="POST" action="saveQuery.php" id="saveArea" class="inputform">
            <h2>Save Query</h2>
            <p>Please fill in the below fields to save your query:</p>
            <table width="80%" style="margin: 0 auto;">
                <tr>
                    <td width="50%">
                        Query Name:<br/>
                        <input type="text" style="width: 300px;" name="sName"/><br/>
                        Query Description:<br/>
                        <textarea name="sDescription" cols="31" rows="8"></textarea><br/>
                        <input type="text" style="width: 300px;" hidden="true" name="sEndpoint" id="sEndpoint" /><br/>
                        <textarea name="sQuery" hidden="true" cols="31" rows="8" id="sQuery"></textarea><br/>
                    </td>
                    <td width="50%" valign="top">
                        Endpoint Classification:<br/>
                        <select name="sClassification">
                            <option value="Media">Media</option>
                            <option value="Geographic">Geographic</option>
                            <option value="Publication">Publication</option>
                            <option value="UserGeneratedContent">UserGeneratedContent</option>
                            <option value="Government">Government</option>
                            <option value="CrossDomain">CrossDomain</option>
                            <option value="LifeSciences">LifeSciences</option>
                            <option value="Other">Other</option>
                            <option value="SPARQLr">SPARQLr</option>
                        </select><br/>
                        Tags (separate with commas):<br/>
                       <textarea name="sTags"cols="31" rows="8" id="sTags"></textarea><br/>
                    </td>
                </tr>
            </table>
            <input type="submit" value="SAVE QUERY" class="button"/>
        </form>
     </div>   
     <br/>
</section>
<script src="js/main.js"></script>
<?php
	require_once('footer.php');
?>
