<section class="content-header">
    <h1>
        Daftar Label Harga
    </h1>
    <ol class="breadcrumb">
        <li><a href="<?= site_url('dashboard') ?>"><i class="fa fa-dashboard"></i> Home</a></li>
        <li class="active">Daftar Label Harga</li>
    </ol>
</section>
<div class="content">
    <div class="row">
        <div class="col-lg-12 col-sm-12 col-12">
            <div class="box box-danger">
                <div class="box-body">
                    <form class="form-horizontal" 
                        method="get" 
                        action="<?= site_url('reports/label_harga/print') ?>"
                        target="_blank">
                        <div class="form-group <?= form_error('category_id') ? 'has-error' : '' ?>">
                            <label class="col-lg-3 col-sm-3 control-label">Kategori</label>
                            <div class="col-lg-9 col-sm-9">
                                <div class="col-lg-6 col-sm-8">
                                    <select name="cat_id" class="form-control" id="category-choices">
                                        <option selected disabled="disabled">Pilih Kategori</option>
                                        <?php foreach ($categories as $category) : ?>
                                        <option value="<?= $category->category_id ?>"><?= $category->name ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <div class="col-sm-offset-3 col-lg-3 col-sm-3" style="padding-left: 30px;">
                                <button type="submit" class="btn btn-info" id="btn-search">
                                    <i class="fa fa-print" aria-hidden="true"></i> Print
                                </button>
                            </div>
                        </div>                                                
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>