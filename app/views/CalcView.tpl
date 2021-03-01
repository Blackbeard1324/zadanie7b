{extends file="main.tpl"}

{block name=content}

<div class="pure-menu pure-menu-horizontal bottom-margin">
	<a href="{$conf->action_url}logout"  class="pure-menu-heading pure-menu-link">wyloguj</a>
	<span style="float:right;">użytkownik: {$user->login}, rola: {$user->role}</span>
</div>

<form action="{$conf->action_root}calcCompute" method="post">
	<fieldset>
		<div class="pure-control-group">
			<label for="id_kwota">Kwota pożyczki: </label>
			<input id="id_kwota" type="text" name="kwota" value="{$form->kwota}" />
		</div>
	<div class="pure-control-group">
		<label for="id_lata">Na ile lat: </label>
		<input id="id_lata" type="text" name="lata" value="{$form->lata}" />
	</div>
	<div class="pure-control-group">
		<label for="id_procent">Oprocentowanie pożyczki: </label>
		<input id="id_procent" type="text" name="procent" value="{$form->procent}" />
	</div>
		<div class="pure-controls">
			<input type="submit" value="Oblicz" class="pure-button pure-button-primary"/>
		</div>
	</fieldset>
</form>

	{include file='messages.tpl'}
	{if isset($res->result)}
		<div class="messages info">
			Twoja miesięczna rata to: {$res->result}
		</div>
	{/if}

{/block}