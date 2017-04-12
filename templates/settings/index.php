<div id="app-settings">
    <div id="app-settings-header">
        <button class="settings-button" data-apps-slide-toggle="#app-settings-content"></button>
    </div>
    <div id="app-settings-content">
        <?php p($l->t('Grouping')); ?>
        <fieldset>
            <input class="grouping-option" type="radio" id="none" name="none" value="none" checked="true">
            <label for="mc"> <?php p($l->t('None')); ?></label><br>

            <input class="grouping-option" type="radio" id="related-id" name="related-id" value="related-id">
            <label for="mc"> <?php p($l->t('Related ID')); ?></label>
        </fieldset>
    </div>
</div>
