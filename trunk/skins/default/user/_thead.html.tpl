            <div class="label">{lang id="USER.17"}:</div>
            <ul class="comment">
<!-- {if !empty($USER.USER_INSERTED)} -->
                <li class="li_hint">{lang id="USER.PROFIL.1"} {$USER.USER_INSERTED|date} {if !empty($USER.COUNT_LOGINS)}{lang id="USER.PROFIL.1A"}{/if}</li>
<!-- {/if} -->
<!-- {if !empty($USER.USER_LOGIN_LAST)} -->
                <li class="li_pointer">{lang id="USER.PROFIL.2"} {$USER.USER_LOGIN_LAST|date}</li>
<!-- {/if} -->
<!-- {if !empty($USER.USERPROFILE_MODIFIED)} -->
                <li class="li_edit">{lang id="USER.PROFIL.3"} {$USER.USERPROFILE_MODIFIED|date}</li>
<!-- {/if} -->
<!-- {if !empty($USER.USER_GENDER)} -->
  <!-- {if $USER.USER_GENDER=="M"} -->
                <li class="li_male">{lang id="USER.OPTION.20"}</li>
  <!-- {elseif $USER.USER_GENDER=="F"} -->
                <li class="li_female">{lang id="USER.OPTION.21"}</li>
  <!-- {elseif $USER.USER_GENDER=="G"} -->
                <li class="li_group">{lang id="USER.OPTION.22"}</li>
  <!-- {/if} -->
<!-- {/if} -->
            </ul>
