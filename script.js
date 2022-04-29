var Project4 = (function () {
        // http://localhost:8080/CS325-Class-Registration-Page-PHP/index.html
        var searchurl = "http://ec2-3-143-211-101.us-east-2.compute.amazonaws.com/CS325_Project3/search?";
        var listurl = "http://ec2-3-143-211-101.us-east-2.compute.amazonaws.com/CS325_Project3/list?";

        var jsonsubjects, jsonscheduleTypes = null;
        var userinput = {
                "subjectid": null,
                "num": null,
                "title": null,
                "scheduletypeid": null,
                "start": null,
                "end": null,
                "days": [],
                "beginmm": null,
                "beginhh": null,
                "endhh": null,
                "endmm": null,
        };


        return {
                start: function () {
                        var that = this;

                        // get all the data from api to populate the forms

                        $.ajax({
                                url: 'http://ec2-3-143-211-101.us-east-2.compute.amazonaws.com/CS325_Project3/list?type=subject',
                                method: 'GET',
                                dataType: 'json',
                                success: function (response) {
                                        jsonsubjects = response;
                                        console.log("jsonsubjects---" + jsonsubjects);
                                        that.initSubjects();

                                }

                        })
                        $.ajax({
                                url: 'http://ec2-3-143-211-101.us-east-2.compute.amazonaws.com/CS325_Project3/list?type=scheduletype',
                                method: 'GET',
                                dataType: 'json',
                                success: function (response) {
                                        jsonscheduleTypes = response;
                                        console.log("scheduletypes---" + jsonscheduleTypes);
                                        that.initSchedule();

                                }
                        })

                        //add number selects to hours and minutes
                        $("#begin_hh").append("<option value = default> ");
                        for (var i = 1; i < 13; i++) {
                                if (i < 10) {

                                        $("#begin_hh").append("<option value = 0" + i + ">" + i);
                                }
                                else {
                                        $("#begin_hh").append("<option value = " + i + ">" + i);

                                }
                        }
                        $("#begin_mm").append("<option value = default> ");
                        for (var i = 0; i < 60; i += 15) {
                                if (i < 10) {

                                        $("#begin_mm").append("<option value = 0" + i + ">" + i);
                                }
                                else {
                                        $("#begin_mm").append("<option value = " + i + ">" + i);

                                }
                        }

                        $("#end_hh").append("<option value = default> ");
                        for (var i = 1; i < 13; i++) {
                                if (i < 10) {

                                        $("#end_hh").append("<option value = 0" + i + ">" + i);
                                }
                                else {
                                        $("#end_hh").append("<option value = " + i + ">" + i);

                                }
                        }
                        $("#end_mm").append("<option value = default> ");
                        for (var i = 0; i < 60; i += 15) {
                                if (i < 10) {

                                        $("#end_mm").append("<option value = 0" + i + ">" + i);
                                }
                                else {
                                        $("#end_mm").append("<option value = " + i + ">" + i);

                                }
                        }
                },


                /*Initialize the jsonsubjects, schedule types mthods and insturctor lists*/
                initSubjects: function () {
                        for (var i = 0; i < jsonsubjects[0].length; i++) {

                                $("#subj_id").append("<option value=" + jsonsubjects[0][i] + ">" + jsonsubjects[1][i]);
                        }
                },
                initSchedule: function () {
                        $("#scheduletypeid").append("<option value = default>");
                        for (var i = 0; i < jsonscheduleTypes[0].length; i++) {

                                $("#scheduletypeid").append("<option value=" + jsonscheduleTypes[0][i] + ">" + jsonscheduleTypes[1][i]);
                        }
                },


                fail: function () {

                },

                //output results

                //get info from the user and get info from api
                submitForm: function () {
                        userinput.subjectid = $("#subj_id").val().trim();
                        userinput.num = $("#num").val().trim();
                        userinput.title = $("#title").val().trim();

                        userinput.scheduletypeid = $("#scheduletypeid").val().trim();


                        userinput.beginmm = $("#begin_mm").val().trim();
                        userinput.beginhh = $("#begin_hh").val().trim();
                        //convert to 24 hour time
                        if ($("#begin_ap").val().trim() == "pm") {
                                userinput.beginhh = Number(userinput.beginhh) + Number(12);
                        }


                        userinput.endmm = $("#end_mm").val().trim();
                        userinput.endhh = $("#end_hh").val().trim();
                        //convert to 24 hour time
                        if ($("#end_ap").val().trim() == "pm") {
                                userinput.endhh = Number(userinput.endhh) + Number(12);
                        }

                        //make start and end string
                        userinput.start = userinput.beginhh + ":" + userinput.beginmm + ":00";
                        userinput.end = userinput.endhh + ":" + userinput.endmm + ":00";

                        delete userinput.beginhh;
                        delete userinput.beginmm;
                        delete userinput.endhh;
                        delete userinput.endmm;

                        //days
                        var daysselected = false;
                        var days = [];
                        if ($("#sel_mwf")[0].checked) {
                                days.push("MWF");
                                if (!daysselected) {
                                        daysselected = true;
                                }
                        }
                        if ($("#sel_tr")[0].checked) {
                                days.push("TR");
                                if (!daysselected) {
                                        daysselected = true;
                                }
                        }
                        if ($("#sel_any")[0].checked) {
                                days = ["ANY"];
                                if (!daysselected) {
                                        daysselected = true;
                                }
                        }


                        if (!daysselected) {
                                days.push("ANY");
                        }

                        userinput.days = days;


                        //diagnostic print
                        for (const key in userinput) {

                                console.log(`${key}: ${userinput[key]}`);
                        }

                        //validate
                        if (userinput.subjectid != null) {
                                this.findChangedValues();
                        }
                        else {
                                alert("You must select a subject");
                        }

                },

                findChangedValues: function () {
                        console.log("requesting sent\n\n\n");

                        var customurl = null;
                        var isDefaultVal = false;
                        //count of values that have no been altered by user
                        var defaultcount = 0;

                        //holds the keys of values in User Input that have changed
                        var keysOfChangedUserInputValues = [];
                        //get the keys of all the changed values + num and title
                        for (const key in userinput) {

                                if (userinput[key] != "default" && userinput[key] != "default:default:00" && userinput[key] != "ANY" && userinput[key] != "") {
                                        //dianostic print
                                        console.log("changed value---", key, userinput[key]);
                                        //add key to changed keys list
                                        keysOfChangedUserInputValues.push(key);




                                } else {
                                        defaultcount += 1;
                                }
                                //magic constants are bad i know but at this point i just need it to work
                                //i can refactor when i dont have a due date lol
                                if (defaultcount == 10) {
                                        isDefaultVal = true;
                                }
                        }
                        console.log("default is", isDefaultVal);
                        //if everything is default then compose this get url
                        if (isDefaultVal) {
                                customurl = searchurl;
                                customurl += "subjectid=" + userinput.subjectid;

                                console.log("\ncustomurl---", customurl);
                        } else {
                                customurl = searchurl;
                                for (var i = 0; i < keysOfChangedUserInputValues.length; i++) {

                                        customurl += keysOfChangedUserInputValues[i] + "=" + userinput[keysOfChangedUserInputValues[i]];
                                        customurl += "&";
                                }

                        }

                        //diagnostic print
                        console.log("\n\n\n");
                        for (const key in keysOfChangedUserInputValues) {

                                console.log(`keys of changed values-- ${key}: ${keysOfChangedUserInputValues[key]}`);
                        }
                        this.makeGetRequest(customurl);
                },

                makeGetRequest: function (customurl) {
                        var that = this;
                        console.log("\n\n\nmake get request---\n Request url---v\n" + customurl);
                        $.ajax({
                                url: customurl,
                                method: 'GET',
                                dataType: 'json',
                                success: function (response) {
                                        var temp = response;
                                        console.log("\nresponse data---" + temp);
                                        that.outputResults(temp);
                                }

                        })

                },
                outputResults: function (data) {
                        console.log("\noutput reseult data", data);
                        $("#output").html("<p>Course Found:</p>");

                        if (data.length == 0) {
                                $("#output").append("<p> NO Course were found</p>");
                        } else {


                                data.forEach(element => {
                                        /*
                                        for (key in element) {
                                                $("#output").append("<p>" + key + "-----" + element[key] + "</p>");
                                        }
                                        */
                                        $("#output").append("<p>" + element["description"] + " - " + element["crn"] + " - " + element["subjectid"] + " " + element["num"] + " - " + element["section"] + "</p>");
                                        $("#output").append("<p>" + "Course Level:   " + element["level"] + "</p>");
                                        $("#output").append("<p>" + "Credit Hours:   " + element["credits"] + "</p>");
                                        $("#output").append("<p>" + "Term ID:   " + element["termid"] + "</p>");
                                        $("#output").append("<p>" + "Course Level:   " + element["level"] + "</p>");
                                        $("#output").append("<p>" + "Course Type:   " + element["scheduletype"] + "</p>");
                                        $("#output").append("<p>" + "Instructor:   " + element["instructor"] + "</p>");
                                        $("#output").append("<p>" + "Location:   " + element["where"] + "</p>");
                                        $("#output").append("<p>" + "Start Time: " + element["start"] + "  " + "     End Time" + element["end"] + "</p>");
                                        $("#output").append("<p>" + "Class Days: " + element["days"] + "</p>");

                                        $("#output").append("<br>");
                                        $("#output").append("<br>");
                                        $("#output").append("<br>");



                                });
                        }
                },

                //reset the page
                reset: function () {
                        let confirmAction = confirm("Would you like to reset your selections?");
                        if (confirmAction) {
                                window.location.reload();
                        }

                },

        }


})();