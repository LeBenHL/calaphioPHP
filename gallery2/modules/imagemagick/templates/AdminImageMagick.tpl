{*
 * $Revision: 1.33 $
 * If you want to customize this file, do not edit it directly since future upgrades
 * may overwrite it.  Instead, copy it into a new directory called "local" and edit that
 * version.  Gallery will look for that file first and use it if it exists.
 *}
<div class="gbBlock gcBackground1">
  <h2> {g->text text="ImageMagick Settings"} </h2>
</div>

{if isset($status.saved)}
<div class="gbBlock"><h2 class="giSuccess">
  {g->text text="Settings saved successfully"}
</h2></div>
{/if}

<div class="gbBlock">
  <p class="giDescription">
    {g->text text="ImageMagick is a graphics toolkit that can be used to process images that you upload to Gallery.  You must install the ImageMagick binaries on your server, then enter the path to them in the text box below.  If you're on a Unix machine, don't forget to make the binaries executable (<i>chmod 755 *</i> in the ImageMagick directory should do it)"}
  </p>

  <table class="gbDataTable"><tr>
    <td>
      {g->text text="Directory to ImageMagick/GraphicsMagick binaries:"}
    </td><td>
      <input type="text" id='giFormPath' size="40" autocomplete="off"
       name="{g->formVar var="form[path]"}" value="{$form.path}"/>
      {g->autoComplete element="giFormPath"}
	{g->url arg1="view=core.SimpleCallback" arg2="command=lookupDirectories"
		arg3="prefix=__VALUE__" forJavascript="true"}
      {/g->autoComplete}

      {if isset($form.error.path.missing)}
      <div class="giError">
	{g->text text="You must enter a path to your ImageMagick binaries"}
      </div>
      {/if}
      {if isset($form.error.path.bad)}
      <div class="giError">
	{g->text text="The path you entered is not a valid directory or is not accessible."}
      </div>
      {/if}
      {if isset($form.error.path.testError)}
      <div class="giError">
	{g->text text="The path you entered doesn't contain valid ImageMagick binaries. Use the 'test' button to check where the error is."}
      </div>
      {/if}
      {if isset($form.error.path.badPath)}
      <div class="giError">
	{g->text text="The path you entered isn't a valid path."}
      </div>
      {/if}
    </td>
  </tr><tr>
    <td>
      {g->text text="JPEG Quality:"}
    </td><td>
      <select name="{g->formVar var="form[jpegQuality]"}">
	{html_options values=$AdminImageMagick.jpegQualityList
	 selected=$form.jpegQuality output=$AdminImageMagick.jpegQualityList}
      </select>
    </td>
  {if $form.cmykSupport!="none"}
  </tr><tr>
    <td>
      {g->text text="CMYK JPEG Support:"}
    </td><td>
      <input type="checkbox"{if $form.cmykSupport=="on"} checked="checked"{/if}
       onclick="document.getElementById('cmykSupport').value = this.checked ? 'on' : 'off'"/>
    </td>
  {/if}
  </tr></table>
  <input type="hidden" id="cmykSupport"
   name="{g->formVar var="form[cmykSupport]"}" value="{$form.cmykSupport}"/>
</div>

<div class="gbBlock gcBackground1">
  <input type="submit" class="inputTypeSubmit"
   name="{g->formVar var="form[action][save]"}" value="{g->text text="Save Settings"}"/>
  <input type="submit" class="inputTypeSubmit"
   name="{g->formVar var="form[action][test]"}" value="{g->text text="Test Settings"}"/>
  {if $AdminImageMagick.isConfigure}
    <input type="submit" class="inputTypeSubmit"
     name="{g->formVar var="form[action][cancel]"}" value="{g->text text="Cancel"}"/>
  {else}
    <input type="submit" class="inputTypeSubmit"
     name="{g->formVar var="form[action][reset]"}" value="{g->text text="Reset"}"/>
  {/if}
</div>

{if !empty($AdminImageMagick.tests)}
<div class="gbBlock">
  <h3> {g->text text="ImageMagick binary test results"} </h3>

  <table class="gbDataTable"><tr>
    <th> {g->text text="Binary Name"} </th>
    <th> {g->text text="Pass/Fail"} </th>
  </tr>
  {foreach from=$AdminImageMagick.tests item=test}
    <tr class="{cycle values="gbEven,gbOdd"}">
      <td>
	{$test.name}
    </td><td>
	{if ($test.success)}
	  <div class="giSuccess">
	    {g->text text="Passed"}
	  </div>
	{else}
	  <div class="giError">
	    {g->text text="Failed"}
	  </div>
	  {if !empty($test.message)}
	    {g->text text="Error messages:"}
	    <br/>
	    <div class="giError">
	      {foreach from=$test.message item=line}
		<pre>{$line}</pre>
	      {/foreach}
	    </div>
	  {/if}
	{/if}
      </td>
    </tr>
  {/foreach}
  </table>
</div>

{if $AdminImageMagick.mimeTypes}
<div class="gbBlock">
  <div class="giTitle"> {g->text text="Version"} </div>
  <p class="giDescription">
    {$AdminImageMagick.version.0} {$AdminImageMagick.version.1}
  </p>

  <div class="giTitle"> {g->text text="Supported MIME Types"} </div>
  <p class="giDescription">
    {g->text text="The ImageMagick module can support files with the following MIME types:"}
  </p>
  <p class="giDescription">
  {foreach from=$AdminImageMagick.mimeTypes item=mimeType}
    {$mimeType}<br />
  {/foreach}
  </p>
</div>
{/if}

{if ($AdminImageMagick.failCount > 0)}
<div class="gbBlock">
  <h3>
    {g->text one="Debug output (%d failed test)" many="Debug output (%d failed tests)"
	     count=$AdminImageMagick.failCount arg1=$AdminImageMagick.failCount}
    <span id="AdminImageMagick_trace-toggle"
     class="giBlockToggle gcBackground1 gcBorder2" style="border-width: 1px"
     onclick="BlockToggle('AdminImageMagick_debugSnippet', 'AdminImageMagick_trace-toggle')">+</span>
  </h3>
  <p class="giDescription">
    {g->text text="We gathered this debug output while testing your ImageMagick binaries.  If you read through this carefully you may discover the reason why your ImageMagick binaries failed the tests."}
  </p>
  <pre id="AdminImageMagick_debugSnippet" class="gcBackground1 gcBorder2"
   style="display: none; border-width: 1px; border-style: dotted; padding: 4px">
    {$AdminImageMagick.debugSnippet}
  </pre>
</div>
{/if}
{/if}
