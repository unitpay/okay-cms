{$meta_title = "UnitPay"|escape scope=global}

{*Название страницы*}
<div class="row">
    <div class="col-lg-12 col-md-12">
        <div class="wrap_heading">
            <div class="box_heading heading_page">
                UnitPay
            </div>
        </div>
    </div>
    <div class="col-md-12 col-lg-12 col-sm-12 float-xs-right"></div>
</div>

<div class="row">
    <div class="col-xs-12">
        <div class="boxed">
            <div class="">
                <div>
                    <p>
                        {$btr->okaycms__unit_pay__description_1} <b>{url_generator route="OkayCMS.UnitPay.Callback" absolute=true}</b> {$btr->okaycms__unit_pay__description_2}
                    </p>
                </div>

                <div>
                    <img src="{$rootUrl}/Okay/Modules/OkayCMS/UnitPay/Backend/images/unitpay.png">
                </div>
            </div>
        </div>
    </div>
</div>
