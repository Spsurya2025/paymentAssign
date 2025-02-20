<script>
    $(document).ready(function () {
    $("#trnscto").change(function () {
        const transaction_to = $(this).val();
        const organisation_id = $("#pay_orgnstn").val();
        const trnsctn_typ = $("#trnsc_type").val();
        const $select = $("#request_num")[0].selectize;
        const submitButton = $("#submitBtn"); // Assuming the button has an ID 'submitBtn'
        
        $select.clear();
        $select.clearOptions();
        $select.refreshOptions();
        $("#showPay").html('');
        $("#payment_req_id").val('');

        // Disable the submit button
        submitButton.prop("disabled", true);

        let apiEndpoints = {};
        let requestData = {};

        if (trnsctn_typ.toUpperCase() === 'DEBIT') {
            apiEndpoints = {
                "Supplier": "supplier_pay_assign/get_spl.php",
                "Vendor": "Vendor_pay_assign/get_ven.php",
                "Operator": "operator_pay_assign/get_opr.php",
                "Transporter": "transporter_pay_assign/get_tr.php",
                "Salary Processing": "salary_pay_assign/get_sal.php",
                "Expense": "exp_pay_assign/get_exp.php",
                "Others": "other_pay_assign/get_oth.php",
                "Rent": "rent_pay_assign/get_rent.php",
                "FD": "fd_pay_assign/fd_payassign.php",
                "Collection": "colctn_pay_assign/get_col.php",
            };

            if (!apiEndpoints[transaction_to]) return;
            let dataType = transaction_to === "FD" ? "html" : "json";

            $.ajax({
                url: apiEndpoints[transaction_to],
                data: { trans_to: transaction_to, organisation_id: organisation_id },
                type: 'GET',
                dataType: dataType,
                success: function (response) {
                    if (transaction_to === "FD") {
                        $("#showPay").html($.trim(response));
                    } else {
                        if (response.length === 0) {
                            alert('No data available');
                            return;
                        }
                        handleResponse(response, $select, transaction_to);
                    }
                    submitButton.prop("disabled", false); // Enable the button after success
                },
                error: function () {
                    alert('Failed to fetch data');
                    submitButton.prop("disabled", false); // Enable even on error
                }
            });
        } else if (trnsctn_typ.toUpperCase() === 'CREDIT') {
            apiEndpoints = {
                "Supplier": "<?php echo SITE_URL; ?>/basic/finance/payment_assign/supplier_pay_assign/cr_supplier_payasn.php",
                "Vendor": "<?php echo SITE_URL; ?>/basic/finance/payment_assign/Vendor_pay_assign/cr_vendor_payasign.php",
                "Transporter": "<?php echo SITE_URL; ?>/basic/finance/payment_assign/transporter_pay_assign/cr_transport_pay_assign.php",
                "Expense": "<?php echo SITE_URL; ?>/basic/finance/payment_assign/exp_pay_assign/cr_exp_payassign.php",
                "Salary Processing": "<?php echo SITE_URL; ?>/basic/finance/payment_assign/salary_pay_assign/cr_salary_payassign.php",
                "Operator": "<?php echo SITE_URL; ?>/basic/finance/payment_assign/operator_pay_assign/cr_operator_payasgn.php",
                "Others": "<?php echo SITE_URL; ?>/basic/finance/payment_assign/other_pay_assign/cr_others_payasn.php",
                "Rent": "<?php echo SITE_URL; ?>/basic/finance/payment_assign/rent_pay_assign/cr_rent_payassign.php",
                "FD": "<?php echo SITE_URL; ?>/basic/finance/payment_assign/fd_pay_assign/cr_fd_payassign.php",
                "Collection": "<?php echo SITE_URL; ?>/basic/finance/payment_assign/colctn_pay_assign/cr_col_payassign.php"
            };

            requestData = {
                "Supplier": { bimpid: <?php echo $_GET['bimpid']; ?>, trnsctyp: trnsctn_typ },
                "Vendor": { bimpid: <?php echo $_GET['bimpid']; ?> },
                "Transporter": { bimpid: <?php echo $_GET['bimpid']; ?> },
                "Expense": { bimpid: <?php echo $_GET['bimpid']; ?> },
                "Salary Processing": { bimpid: <?php echo $_GET['bimpid']; ?> },
                "Operator": { bimpid: <?php echo $_GET['bimpid']; ?> },
                "Others": { bimpid: <?php echo $_GET['bimpid']; ?> },
                "Rent": { bimpid: <?php echo $_GET['bimpid']; ?> },
                "FD": { bimpid: <?php echo $_GET['bimpid']; ?>, org_id: <?php echo $fthorg->id; ?> },
                "Collection": { bimpid: <?php echo $_GET['bimpid']; ?>, org_id: <?php echo $fthorg->id; ?> }
            };

            if (!apiEndpoints[transaction_to]) {
                alert("Transaction to/type not available or not implemented");
                submitButton.prop("disabled", false);
                return;
            }

            $.ajax({
                url: apiEndpoints[transaction_to],
                data: requestData[transaction_to],
                type: 'GET',
                success: function (response) {
                    $("#showPay").html($.trim(response));
                    submitButton.prop("disabled", false); // Enable the button after success
                },
                error: function () {
                    alert('Failed to fetch data');
                    submitButton.prop("disabled", false); // Enable even on error
                }
            });
        }
    });

    function handleResponse(response, selectizeInstance, transaction_to) {
        response.forEach(function (item) {
            let prNums = [];

            if (transaction_to === "Salary Processing" || transaction_to === "Expense") {
                prNums = [item.pr_num]; // Single value case
            } else {
                prNums = item.pr_num.split('#'); // Multiple values case
            }

            prNums.forEach(function (prNum) {
                if (prNum.trim() !== "") {
                    selectizeInstance.addOption({
                        value: JSON.stringify({ prNum: prNum, payRequestId: item.pay_request_id || '' }),
                        text: prNum
                    });
                }
            });
        });
        selectizeInstance.refreshOptions();
    }

    $("#request_num").change(function () {
        $("#showPay").html('');
        $("#preqnum").val('');
        const selectedValue = $(this).val();
        if (!selectedValue) return;
        const parsedValue = JSON.parse(selectedValue);
        const request_num = parsedValue.prNum;
        const pay_request_id = parsedValue.payRequestId || '';
        const trnsto = $("#trnscto").val();
        $("#payment_req_id").val(pay_request_id);
        $("#preqnum").val(request_num);

        const apiEndpoints = {
            "Supplier": "supplier_pay_assign/supplier_payasgn.php",
            "Vendor": "Vendor_pay_assign/vendor_payasign.php",
            "Operator": "operator_pay_assign/operator_payasgn.php",
            "Transporter": "transporter_pay_assign/transport_pay_assign.php",
            "Salary Processing": "salary_pay_assign/salary_payassign.php",
            "Expense": "exp_pay_assign/exp_payassign.php",
            "Others": "other_pay_assign/others_payasn.php",
            "Rent": "rent_pay_assign/rent_payassign.php",
            "Collection": "colctn_pay_assign/col_payassign.php"
        };

        if (!apiEndpoints[trnsto]) return;

        const requestData = trnsto === "Salary Processing" || trnsto === "Expense"
            ? { request_num: request_num }
            : { py_req_id: pay_request_id, request_num: request_num };

        $.ajax({
            url: apiEndpoints[trnsto],
            data: requestData,
            type: 'GET',
            success: function (response) {
                $("#showPay").html($.trim(response));
            },
            error: function () {
                alert(`Failed to fetch ${trnsto.toLowerCase()} data`);
            }
        });
    });
});

</script>