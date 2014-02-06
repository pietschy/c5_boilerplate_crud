<?php defined('C5_EXECUTE') or die(_("Access Denied."));

$dh = Loader::helper('concrete/dashboard');
$ih = Loader::helper('concrete/interface');
$disabled = isset($disabled) && $disabled;
?>

<?=$dh->getDashboardPaneHeaderWrapper('Delete Body Type', false, 'span6 offset3', false)?>

	<div class="ccm-pane-body">
		<?php if (!$disabled): ?>
		<h3>Are you sure you wish to permanently delete the following body type?</h3>
		<?php endif ?>
	
		<table class="form-table">
			<tr>
				<td class="right">Name:</td>
				<td><?=h($name)?></td>
			</tr>
		</table>
	</div>
	
	<div class="ccm-pane-footer">
		<form method="post" action="<?=$this->action('body_types_delete', (int)$id)?>" style="margin: 0;">
			<?=$token?>
			<?=($disabled ? '' : $ih->submit('Delete', false, 'right', 'error'))?>
			<?=$ih->button('Cancel', $this->action('body_types_list'), 'left')?>
		</form>
	</div>

<?=$dh->getDashboardPaneFooterWrapper()?>
