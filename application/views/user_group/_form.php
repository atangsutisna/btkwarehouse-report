<form role="form" method="post" id="form-user-group"
    class="form-horizontal" v-on:submit.prevent>   
    <div class="box-body">
        <alert-component 
            v-bind:alert="form_status.alert" 
            v-bind:has_errors="form_status.has_errors"
            v-bind:success="form_status.success"
            v-bind:message="form_status.message">
        </alert-component>       
        <?php if (isset($user_group_id)) : ?>
            <input type="hidden" name="user_group_id" value="<?= $user_group_id ?>"/>
        <?php endif; ?>                 
        <div class="form-group">
            <label class="col-lg-3 col-sm-3 control-label">Nama Group</label>
            <div class="col-lg-3 col-sm-3">
                <input type="text" name="name" 
                    class="form-control" v-model="name"/>
                <?= form_error('username') ?>
            </div>
        </div>                        
        <div class="form-group">
            <div class="col-lg-offset-3 col-sm-offset-3" style="margin-left: 26.5%;">
                <button type="submit" class="btn btn-primary" id="btn-save" v-on:click="save">Simpan</button>
            </div>
        </div>
    </div>
</form>