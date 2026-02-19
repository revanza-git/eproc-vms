<div class="tab procView">
    <?php echo $this->utility->tabNav($tabNav, 'kontrak'); ?>

    <div class="tableWrapper" style="margin-bottom: 20px;padding-left: 20px;">
        <?php echo $this->session->flashdata('msgSuccess') ?>

        <div class="formDashboard">
            <form method="POST" enctype="multipart/form-data">
                <table>
                    <tr class="input-form">
                        <td><label>Umpan Balik </label></td>
                        <td>
                            : <?php echo $get_feedback['remark']; ?>
                        </td>
                    </tr>
                    <?php if ($get_feedback['reply'] != null) { ?>
                        <tr class="input-form">
                            <td><label>Balasan</label></td>
                            <td>
                                : <?php echo ($get_feedback['reply'] == null) ? '-' : $get_feedback['reply']; ?>
                            </td>
                        </tr>
                    <?php } ?>
                    <?php if ($get_feedback['reply'] == null) { ?>
                        <tr class="input-form">
                            <td><label>Pesan*</label></td>
                            <td>
                                <textarea name="reply"></textarea>
                                <?php echo form_error('reply'); ?>
                            </td>
                        </tr>
                    <?php } ?>
                </table>
                <?php if ($get_feedback['reply'] == null) { ?>
                    <div class="buttonRegBox clearfix">
                        <input type="submit" value="Simpan" class="btnBlue" name="Simpan">
                    </div>
                <?php } else { ?>
                    <a href="<?= site_url('feedback') ?>" class="btnBlue">Kembali</a>
                <?php } ?>
            </form>
        </div>
    </div>
</div>