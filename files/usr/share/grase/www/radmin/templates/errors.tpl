{if $error}
			<div class="ui-widget" id="errormessages">
				<div class="ui-state-error ui-corner-all" > 
					<p class="errorMessage"><span class="ui-icon ui-icon-alert"></span> 
					<strong>Error:</strong> <ul>{foreach from=$error item=msg}<li>{$msg}</li>{/foreach}</ul></p>

				</div>
			</div>
{/if}

{if $success}
			<div class="ui-widget" id="successmessages">
				<div class="ui-state-highlight ui-corner-all" > 
					<p class="successMessage"><span class="ui-icon ui-icon-alert"></span> 
					<ul>{foreach from=$success item=msg}<li>{$msg}</li>{/foreach}</ul></p>

				</div>
			</div>
{/if}
