<?php
/**
 *
 * PHP 5
 *
 * Copyright (C) HMS Team
 *
 * Licensed under The MIT License
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright     HMS Team
 * @package       dev.Setup.Web
 * @license       MIT License (http://www.opensource.org/licenses/mit-license.php)
 */

require ('./header.html');
?>
		<form id="form_646271" class="appnitro"  method="post" action="setup.php">
			<ul >
				<li id="li_1" >
					<label class="description" for="firstname">First Name: </label>
					<div>
						<input id="firstname" name="firstname" class="element text medium" type="text" maxlength="255" value=""/> 
					</div> 
				</li>
				<li id="li_2" >
					<label class="description" for="surname">Surname: </label>
					<div>
						<input id="surname" name="surname" class="element text medium" type="text" maxlength="255" value=""/> 
					</div> 
				</li>
				<li id="li_3" >
					<label class="description" for="email">Email: </label>
					<div>
						<input id="email" name="email" class="element text medium" type="text" maxlength="255" value=""/> 
					</div> 
				</li>
				<li id="li_4" >
					<label class="description" for="username">Username: </label>
					<div>
						<input id="username" name="username" class="element text medium" type="text" maxlength="255" value=""/> 
					</div> 
				</li>
				<li id="li_5" >
					<label class="description" for="element_4">Settings: </label>
					<span>
						<input id="createdb" name="createdb" class="element checkbox" type="checkbox" checked="checked" />
						<label class="choice" for="createdb">Create Databases and tables</label>
						<input id="setuptmpfolders" name="setuptmpfolders" class="element checkbox" type="checkbox" checked="checked" />
						<label class="choice" for="setuptmpfolders">Setup Temp Folders</label>
						<input id="usedevelopmentenv" name="usedevelopmentenv" class="element checkbox" type="checkbox" checked="checked" />
						<label class="choice" for="usedevelopmentenv">Use development configs, databases and settings</label>
						<input id="realkrb" name="realkrb" class="element checkbox" type="checkbox" />
						<label class="choice" for="realkrb">Real KRB Auth (keep this off unless you know what you're doing)</label>
					</span>
				</li>
				<li class="buttons">
					<input type="hidden" name="form_id" value="646271" />
					<input id="saveForm" class="button_text" type="submit" name="submit" value="Submit" />
				</li>
			</ul>
		</form>	

<?php
require ('./footer.html');