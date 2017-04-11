<?php if ($this->_var['pager']): ?>
<section class="list-pagination">
    <div style="" class="pagenav-wrapper" id="J_PageNavWrap">
      <div class="pagenav-content">
        <div class="pagenav" id="J_PageNav">

          <div class="p-prev p-gray"  style="height:30px;line-height:30px; text-align:center;"> <a href="<?php echo $this->_var['pager']['page_prev']; ?>"><?php echo $this->_var['lang']['page_prev']; ?></a> </div>
          
         <div class="p-next" style="height:30px;line-height:30px; text-align:center;" > <a  href="<?php echo $this->_var['pager']['page_next']; ?>"><?php echo $this->_var['lang']['page_next']; ?></a> </div>
		</div>
      </div>
    </div>
  </section>
<?php endif; ?>