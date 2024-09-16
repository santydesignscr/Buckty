<?php $this->load->view('header');?>
<div id="js_login_again" class="mini_pop">
  <div class="overlay"></div>
  <div class="modal_container">
    <div class="modal">
      <div class="modal_header">
        <h1 class="modal_title">Login</h1>
      </div>
      <div class="modal_content">
          <div class="little_info">
            <div class="info"><span>Your session was expired! </span></div>
            <button class="button primary blue" onclick="location.reload();">Login Again</button>
            </div>
          </form>
          </div>
      </div>
    </div>
  </div>
</div>
<?php $this->load->view('footer');?>