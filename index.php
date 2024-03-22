<style>
  #uploadContainer > *, #uploadForm > *, #result > *{
    margin-bottom: 10px;
  }

</style>

<h1>Line 圖文選單圖片合併功能</h1>
<h2>圖片設定：</h2>
<form id="uploadForm" enctype="multipart/form-data">
    <!-- 版型選擇 -->
    <label for="layout">選擇版型：</label>
    <select name="layout" id="layout" required>
      <option value="" selected disabled hidden>請選擇</option>
        <option value="large_1_1">大型 1*1</option>
        <option value="large_2_1">大型 2*1</option>
        <!-- <option value="large_1_2">大型 1*2</option> -->
        <!-- <option value="large_2_2">大型 2*2</option> -->
        <!-- <option value="large_3_2">大型 3*2</option> -->
        <option value="small_1_1">小型 1*1</option>
        <option value="small_2_1">小型 2*1</option>
        <option value="small_3_1">小型 3*1</option>
    </select><br>

    <!-- 解析度選擇 -->
    <label for="quality">選擇解析度：</label>
    <select name="quality" id="quality" required>
      <!-- <option value="" selected disabled hidden>請選擇</option> -->
        <option value="high" selected>高</option>
        <option value="medium">中</option>
        <option value="low">低</option>
    </select><br>

    <!-- 上傳圖片的說明 -->
    <p id="uploadDescription"></p>
    <div id="uploadImageDemo"></div>

    <!-- 上傳圖片的容器 -->
    <div id="uploadContainer"></div>

    <input type="submit" value="合併圖片">
</form>
<div id="result"></div>
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
<script>
$(document).ready(function(){
  // 圖片輸入設定
  $('#layout').change(render_upload_description);
  $('#quality').change(render_upload_description);

  // API: 合併圖片
  $('#uploadForm').on('submit', api_get_merge_image);

  function api_get_merge_image(e){
    e.preventDefault(); // 阻止表單的自動提交
    var formData = new FormData(this); // 創建 FormData 對象

    $.ajax({
      url: 'upload.php', // 處理上傳的 PHP 文件
      type: 'POST',
      data: formData,
      contentType: false, // 必須設置為 false
      processData: false, // 必須設置為 false
      success: function(data){
        // console.log(data);
        let img_url = data.downloadUrl;
        $('#result').html(`
          <h2>輸出圖片：</h2>
          <img src=${img_url} width="360" alt="merge-image"></img>
          <br>
          <a href=${img_url} download="merge_image.jpg"><button>下載圖片</button></a>
        `);
      }
    });
  }

  function render_upload_description(){
      // 清空先前的輸入框和上傳圖片說明
      $('#uploadContainer').empty();
      $('#result').empty();
      
      // 上傳圖片說明文字、示意圖
      let description = get_per_image_description($("#layout").val(), $("#quality").val());
      let img_layout_demo = `<img src="uploads/img-layout-${$("#layout").val()}.jpg" width="360" alt="image-layout-demo"></img>`; 
      $('#uploadDescription').html(description);
      $('#uploadImageDemo').html(img_layout_demo);

      // 根據選擇顯示輸入框
      const parts = $("#layout").val().split('_');
      const count = parseInt(parts[1], 10);

      for (let i = 1; i <= count; i++) {
          // 建立小標 Label 和檔案上傳 Input，並設置
          $('<label>')
              .text(`第 ${i} 張圖片：`)
              .attr('for', 'image' + i)
              .appendTo('#uploadContainer');

          $('<input>')
              .attr('type', 'file')
              .attr('required', 'true')
              .attr('name', 'image' + i)
              .appendTo('#uploadContainer');

          $('#uploadContainer').append('<br>');
      }
  }

  function get_per_image_description(layout, quality){
    if(!layout || !quality){
      return "";
    }

    // 圖片上傳規則
    let imageSize = {
      'large':{
          'high' : [2500, 1686],
          'medium' : [1200, 810],
          'low' : [800, 540]
      },
      'small':{
          'high' : [2500, 843],
          'medium' : [1200, 405],
          'low' : [800, 270]
      }
    };

    let img_info_arr = layout.split("_");
    let img_amount = img_info_arr[1]*img_info_arr[2];
    let img_width = imageSize[img_info_arr[0]][quality][0]/img_info_arr[1];
    let img_height = imageSize[img_info_arr[0]][quality][1]/img_info_arr[2];

    return `<p>請上傳 ${img_amount} 張圖片，每張解析度為 <strong>${Math.floor(img_width)}px * ${Math.floor(img_height)}px</strong>，示意圖：</p>`;
  }

});
</script>