<!-- SECTION -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
<link href="https://fonts.googleapis.com/css2?family=Architects+Daughter&display=swap" rel="stylesheet">
<style>

    body{
        background-image: url(<?= base_url() ?>front_assets/images/FAUXSKO21/Forescout_FAUXSKO21_Main_Page_No_ICONS.png);
        background-attachment: fixed;
        background-size: cover !important;
        background-position: center center !important;
    }

    .icon-home {
        color: white;
        font-size: 1.5em;
        vertical-align: middle;
    }

    .box-home {
        background-color: #444;
        border-radius: 30px;
        background: rgb(0, 47, 112);
        max-width: 200px;
        min-width: 200px;
        min-height: 200px;
        max-height: 200px;
        padding: 15px;
    }
    .box-home_2 {
        background-color: #444;
        border-radius: 30px;
        background: rgb(0, 47, 112);
        max-width: 147px;
        min-width: 119px;
        min-height: 132px;
        max-height: 185px;
        padding: 15px;
        padding: 0px !important;
    }

    .fa {
        font-weight: 900;
    }

    .col-sm-12 {
        margin-bottom: 10px;
    }
    
     @media (min-width: 768px) and (max-width: 1000px)  {
        #home_first_section{
            height: 550px;
        }
    }

    @media (min-width: 1000px) and (max-width: 1400px)  {
        #home_first_section{
            height: 590px;
        }
    }

    @media (min-width: 1400px) and (max-width: 1600px)  {
        #home_first_section{
            height: 700px;
        }
    }

    @media (min-width: 1600px) and (max-width: 1800px)  {
        #home_first_section{
            height: 800px;
        }
    }

    @media (min-width: 1800px) and (max-width: 2200px)  {
        #home_first_section{
            height: 900px;
        }
    }

    @media (min-width: 2200px) and (max-width: 2800px)  {
        #home_first_section{
            height: 1100px;
        }
    }
    @media (min-width: 2800px) and (max-width: 3200px)  {
        #home_first_section{
            height: 1450px;
        }
    }

    @media (min-width: 3200px) and (max-width: 4200px)  {
        #home_first_section{
            height: 1950px;
        }
    }

    @media (min-width: 4200px) and (max-width: 6000px)  {
        #home_first_section{
            height: 2550px;
        }
    }
</style>
<section class="parallax" style="top: 0; padding-top: 20px;">
    <div class="container container-fullscreen" id="home_first_section">
        <div class="text-middle">
            <div class="row">
                <div class="col-md-6 col-md-push-6 col-lg-6 col-lg-push-6">
                    <div class="text-center m-t-0">
                        <h1 style="color: white; font-family: 'proxima-nova', sans-serif; margin-bottom: 0px; font-size: 40px;">Welcome, <?= $this->session->userdata('cname') ?></h1>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-6 col-md-push-6 col-lg-6 col-lg-push-6 col-sm-12" style="text-align: -webkit-center;">
                    <div class="row">
                        <div class="col-md-4 col-sm-12">
                            <a class="icon-home" href="<?= base_url() ?>sessions">
                                <div class="col-lg box-home p-5 text-center">
                                    <img src="<?= base_url() ?>front_assets/images/Session.png" alt="welcome" class="m-t-25" style="width: 95px;">
                                    <br>
                                    <br>
                                    <span>SESSIONS</span>
                                </div>
                            </a>
                        </div>
                        <div class="col-md-4  col-sm-12">
                            <a class="icon-home" href="<?= base_url() ?>sponsor">
                                <div class="col-lg box-home ml-5 mr-5 p-5 text-center">
                                    <img src="<?= base_url() ?>front_assets/images/sponsor.png" alt="welcome" class="m-t-25" style="width: 95px;">
                                    <br>
                                    <br>
                                    <span>Training EXPO</span>
                                </div>
                            </a>
                        </div>
                        <div class="col-md-4  col-sm-12">
                            <a class="icon-home" href="<?= base_url() ?>lounge">
                                <div class="col-lg box-home p-5 text-center">
                                    <img src="<?= base_url() ?>front_assets/images/lounge.png" alt="welcome" class="m-t-25" style="width: 95px;">
                                    <br>
                                    <br>
                                    <span>LOUNGE</span>
                                </div>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-6 col-md-push-6 col-lg-6 col-lg-push-6 m-b-80" style="text-align: -webkit-center;">
                    <div class="col-md-4 col-md-offset-2 col-sm-12 p-b-25">
                        <a class="icon-home" href="#">
                            <div class="col-lg box-home_2 p-0 text-center p-b-25">
                                <img src="<?= base_url() ?>front_assets/images/info.png" alt="welcome" class="m-t-10" style="width: 73px;">
                                <br>
                                <span style="font-size: 12px;">INFORMATION</span>
                            </div>
                        </a>
                    </div>
                    <div class="col-md-4  col-sm-12">
                        <a class="icon-home" href="/support">
                            <div class="col-lg box-home_2 p-0 p-b-25 text-center">
                                <img src="<?= base_url() ?>front_assets/images/settings-gears.png" alt="welcome" class="m-t-10" style="width: 73px;">
                                <br>
                                <span style="font-size: 12px;">SUPPORT</span>
                            </div>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
</section>
<script type="text/javascript">
    $(document).ready(function () {
        var page_link = $(location).attr('href');
        var user_id = <?= $this->session->userdata("cid") ?>;
        var page_name = "User Dashboard";
        $.ajax({
            url: "<?= base_url() ?>home/add_user_activity",
            type: "post",
            data: {'user_id': user_id, 'page_name': page_name, 'page_link': page_link},
            dataType: "json",
            success: function (data) {
            }
        });
    });
</script>
