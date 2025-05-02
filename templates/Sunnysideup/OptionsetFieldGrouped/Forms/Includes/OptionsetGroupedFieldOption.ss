<h3>$Title</h3>
<ul>
<% loop $Options %>
    <li class="$Class" role="$Role" style="list-style-type: none;">
        <input id="$ID" class="radio" name="$Name" type="radio" value="$Value"<% if $isChecked %> checked<% end_if %><% if $isDisabled %> disabled<% end_if %> <% if $Up.Required %>required<% end_if %> />
        <label for="$ID">$Title</label>
    </li>
<% end_loop %>
</ul>
