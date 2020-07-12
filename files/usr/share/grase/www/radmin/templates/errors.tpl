{if $error}
			<div class="ui-widget messagewidget" id="errormessages">
				<div class="ui-state-error ui-corner-all"  style="margin-top: 20px; padding: 0pt 0.7em;" >
					<p><span class="ui-icon ui-icon-alert" style="float: left; margin-right: 0.3em;"></span></p>
					<ul>{foreach from=$error item=msg}<li><strong>{$msg}</strong></li>{/foreach}</ul>

				</div>
			</div>
{/if}

{if $warningmessages}
			<div class="ui-widget messagewidget" id="warningmessages">
				<div class="ui-state-highlight ui-corner-all"  style="margin-top: 20px; padding: 0pt 0.7em;" >
					<p><span class="ui-icon ui-icon-alert" style="float: left; margin-right: 0.3em;"></span></p>
					<ul>{foreach from=$warningmessages item=msg}<li>{$msg}</li>{/foreach}</ul>
				</div>
			</div>
{/if}

{if $success}
			<div class="ui-widget messagewidget" id="successmessages">
				<div class="ui-state-highlight ui-corner-all"  style="margin-top: 20px; padding: 0pt 0.7em;" >
					<p><span class="ui-icon ui-icon-alert" style="float: left; margin-right: 0.3em;"></span></p>
					<ul>{foreach from=$success item=msg}<li>{$msg}</li>{/foreach}</ul>
				</div>
			</div>
{/if}
