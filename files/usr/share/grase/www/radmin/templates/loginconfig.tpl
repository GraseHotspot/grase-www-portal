{include file="header.tpl" Name="Portal Settings" activepage="loginconfig" helptext="Use this page to change login page Settings"}

<script src="/javascript/codemirror/codemirror.js"></script>
<script src="/javascript/codemirror/util/searchcursor.js" type="text/javascript"></script>
<link rel="stylesheet" href="/javascript/codemirror/codemirror.css">
<script src="/javascript/codemirror/mode/css/css.js"></script>
<script src="/javascript/codemirror/mode/xml/xml.js"></script>

<script src="/javascript/codemirror-ui/js/codemirror-ui.js" type="text/javascript"></script>
<link rel="stylesheet" href="/javascript/codemirror-ui/css/codemirror-ui.css" type="text/css" media="screen" />
{literal}    <style type="text/css">
      .CodeMirror {border: 1px solid black;}
    </style>
{/literal}

<h2>{t}Portal Customisation{/t}</h2>

<div id="LoginConfigForm">
<form method="post" action="?" class="generalForm">

    {foreach from=$singleloginoptions item=attributes key=option}
    <div>
        <label for='{$option}'>{$attributes.label}</label>
        {if $attributes.type != 'bool'}
            <input type="text" name="{$option}" id="{$option}" value='{$attributes.value}'/>
        {else}
            <input type="checkbox" name="{$option}" id="{$option}" {if $attributes.value == "TRUE"}checked="checked"{/if}/>
        {/if}
        <span id="{$option}Info">{$attributes.description}</span>
    </div>
    {/foreach}


    {foreach from=$templateoptions item=attributes key=option}
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
                var uiOptions = { path : '/javascript/codemirror-ui/js/', searchMode : 'popup', imagePath: '/javascript/codemirror-ui/images/silk', }
                var codeMirrorOptions = {
                {/literal}
                    mode: "text/{$attributes.type}",
                    lineNumbers: true,

                {literal}}{/literal};

                //then create the editor
                var my{$option} = new CodeMirrorUI(textarea,uiOptions,codeMirrorOptions);

            </script>

    </div>

    {/foreach}

<p class="nojshelp">{t}For each of the following items, if you need multiple values you can submit the form and it will append a blank input below the last valid value{/t}<p>

    {foreach from=$multiloginoptions item=attributes key=option name=multiloop}
    <div>
        <label for='{$option}'>{$attributes.label}</label>
        {foreach from=$attributes.value item=attribute name=attributeloop}
        <div class="jsmultioption"><input type="text" name="{$option}[]" value='{$attribute}'/>
            <span class="jsremove">
                <span class="jsremovebutton"></span>
                <span class="jsaddremovetext" id="addtext"></span>
            </span>
        </div>
        {/foreach}
        <div class="jsmultioption"><input type="text" name="{$option}[]" id="{$option}" value=''/>
            <span class="jsadd">
                <span class="jsaddbutton"></span>
                <span class="jsaddremovetext" id="addtext"></span>
            </span>
        </div>
        <span id="{$option}Info">{$attributes.description}</span>

    </div>
    {/foreach}

    <button type="submit" name="submit">{t}Save Settings{/t}</button>

</form>

</div>

{include file="footer.tpl"}
