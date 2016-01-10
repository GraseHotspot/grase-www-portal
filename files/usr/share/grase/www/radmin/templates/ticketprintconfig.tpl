{include file="header.tpl" Name="Ticket Printing Settings" activepage="ticketprintconfig" helptext="Use this page to change ticket printing settings"}

<script src="/grase/vendor/codemirror/lib/codemirror.js"></script>
<script src="/grase/vendor/codemirror/addon/search/searchcursor.js" type="text/javascript"></script>
<link rel="stylesheet" href="/grase/vendor/codemirror/lib/codemirror.css">
<script src="/grase/vendor/codemirror/mode/css/css.js"></script>
<script src="/grase/vendor/codemirror/mode/xml/xml.js"></script>

<script src="/grase/vendor/codemirror-ui/js/codemirror-ui.js" type="text/javascript"></script>
<link rel="stylesheet" href="/grase/vendor/codemirror-ui/css/codemirror-ui.css" type="text/css" media="screen" />
{literal}    <style type="text/css">
      .CodeMirror {border: 1px solid black;}
    </style>
{/literal}

<h2>{t}Ticket Printing Settings{/t}</h2>

<div id="TicketPrintConfigForm">
<form method="post" action="?" class="generalForm">

    {foreach from=$singleTicketPrintOptions item=attributes key=option}
    <div>
        <label for='{$option}'>{$attributes.label}</label>
        <input {inputtype type=$attributes.type value=$attributes.value} name="{$option}" id="{$option}" {if $attributes.required}required{/if}/>
        <span id="{$option}Info">{$attributes.description}</span>
    </div>
    {/foreach}

    <button type="submit" name="submit">{t}Save Settings{/t}</button>
    <hr/>
    <h3>{t}HTML and CSS templates{/t}</h3>


    {foreach from=$templateTicketPrintOptions item=attributes key=option}
    <div>
        <label for='{$option}'>{$attributes.label}</label>
        <span id="{$option}Info">{$attributes.description}</span>
        {if $attributes.location}<span class="tpllocation">[{$attributes.location}]</span>{/if}
        <br/>


        <textarea name="{$option}" id="{$option}">{$attributes.value}</textarea>

            <script>
                var delay{$option};

                //first set up some variables
                var textarea = document.getElementById('{$option}');
                {literal}
                var uiOptions = { path : '/grase/vendor/codemirror-ui/js/', searchMode : 'popup', imagePath: '/javascript/codemirror-ui/images/silk', }
                var codeMirrorOptions = {
                {/literal}
                    mode: "text/{$attributes.type}",
                    lineNumbers: true,

                {literal}}{/literal};

                //then create the editor
                var my{$option} = new CodeMirrorUI(textarea,uiOptions,codeMirrorOptions);

            </script>

        <button type="submit" name="submit">{t}Save Settings{/t}</button>

    </div>

    {/foreach}



</form>

</div>

{include file="footer.tpl"}
