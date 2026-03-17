<?php
/**
 * admin_settings.php - Admin page to configure Grok API key
 */

if (!ossn_isAdminLoggedin()) {
    ossn_error_page(403, ossn_print('access:denied'));
    redirect(ossn_site_url());
}

$key = ossn_get_plugin_setting('grok_api_key', 'Alkebulan');

?>
<div class="alkebulan-admin-settings">
    <h2>Alkebulan AI - External AI Settings</h2>
    
    <form method="post" action="<?php echo ossn_site_url("action/alkebulan/save_settings"); ?>">
        <?php echo ossn_plugin_view('input/security', []); // CSRF ?>
        
        <label for="grok_api_key">Grok / xAI API Key</label>
        <input type="text" name="grok_api_key" id="grok_api_key" 
               value="<?php echo htmlspecialchars($key ?? ''); ?>" 
               placeholder="gsk_..." style="width:100%; padding:8px;" />
        
        <p class="ossn-text-muted">Get your key at: <a href="https://console.x.ai/" target="_blank">console.x.ai</a></p>
        
        <button type="submit" class="btn btn-primary">Save Settings</button>
    </form>
    
    <p><strong>Note:</strong> After saving, test the chat assistant. If key is invalid, it falls back to local mode.</p>
</div>
