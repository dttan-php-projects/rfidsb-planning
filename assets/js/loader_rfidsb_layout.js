
function initLayout(){
    if(print_type=='pvh_rfid'||print_type=='trim'||print_type=='trim_macy') {
        LayoutMain = new dhtmlXLayoutObject({
            parent: document.body,
            pattern: "3U",
            offsets: {
                top: 65
            },
            cells: [
                {id: "a", header: true, text: "PRODUCTION ORDER FORM", height: heightOrder},        
                {id: "b", header: true, text: "LIST MATERIAL", width: 650},                
                {id: "c", header: true, text: "LIST SO"}
            ]
        });
    } 
    else if(print_type=='ua_cbs'||print_type=='ua_no_cbs'||print_type=='cbs'||print_type=='rfid') {
        LayoutMain = new dhtmlXLayoutObject({
            parent: document.body,
            pattern: "4U",
            offsets: {
                top: 65
            },
            cells: [
                {id: "a", header: true, align: "left", text:"PRODUCTION ORDER FORM", height: heightOrder },
                {id: "b", header: true, text: "LIST INK", width: 300},
                {id: "c", header: true, text: "LIST MATERIAL", width:300},
                {id: "d", header: true, align: "left", text: "LIST SO"}
            ]           
        });
    } 
    else {
        LayoutMain = new dhtmlXLayoutObject({
            parent: document.body,
            pattern: "1C",
            offsets: {
                top: 65
            },
            cells: [
                {id: "a", header: true, text: "INPUT SOLINE"}
            ]
        });
    }      
}//end init layout
