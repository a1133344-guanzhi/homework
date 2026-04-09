<HTML>
<HEAD>
    <TITLE>森林野外求生冒險營報名</TITLE> 
</HEAD>
<BODY BGCOLOR="Beige">

    <CENTER>
        <H1>🌲 2026 森林野外求生冒險營 🌲</H1>
        <P><B>活動日期:2026年07月15日 (三) 至 07月17日 (五)</B></P>
    </CENTER>

    <CENTER>
        <IMG SRC="camp.jpg" WIDTH="30%" ALT="森林求生營地">
    </CENTER>

    <HR> 
    <P>歡迎來到冒險者的天堂！在這裡你將學習到 <B>野外取火</B> 、<B>陷阱製作</B> 與 <B>求生方位辨識</B> 。</P>
    <P><FONT COLOR="RED">※ 注意：報名截止日期為 2026年06月30日，名額有限！</FONT> </P>

    <H3>🗓️ 三天兩夜冒險時程表</H3>
    <TABLE BORDER="1" WIDTH="90%">
        <TR>
            <TH>日期</TH> <TH>時間</TH>
            <TH>活動名稱</TH>
            <TH>課程細節</TH>
            <TH>備註</TH>
        </TR>
        
        <TR>
            <TD ROWSPAN="3"><CENTER>Day 1<BR>07/15</CENTER></TD>
            <TD>09:00 - 12:00</TD>
            <TD>叢林生存導論</TD>
            <TD>環境評估與求生方位辨識實作</TD>
            <TD></TD>
        </TR>
        <TR>
            <TD>13:30 - 16:30</TD>
            <TD><I>基礎紮營實作</I></TD> 
            <TD>帳篷選址、防雨棚架設練習</TD>
            <TD></TD>
        </TR>
        <TR>
            <TD>19:00 - 21:00</TD>
            <TD>星空定位</TD>
            <TD>利用星座辨識北方與導航</TD>
            <TD>需視天氣狀況</TD>
        </TR>

        <TR>
            <TD ROWSPAN="3"><CENTER>Day 2<BR>07/16</CENTER></TD>
            <TD>08:30 - 12:00</TD>
            <TD>野外取火術</TD> 
            <TD>鑽木取火、打火石使用技巧</TD>
            <TD></TD>
        </TR>
        <TR>
            <TD>13:30 - 17:00</TD>
            <TD>陷阱製作</TD> 
            <TD>繩結應用與小型求生陷阱原理</TD>
            <TD>實務操作</TD>
        </TR>
        <TR>
            <TD>19:00 - 20:30</TD>
            <TD>營火談心</TD>
            <TD>野外急救案例分享與應變</TD>
            <TD></TD>
        </TR>

        <TR>
            <TD ROWSPAN="2"><CENTER>Day 3<BR>07/17</CENTER></TD>
            <TD>09:00 - 12:00</TD>
            <TD>全能求生競賽</TD>
            <TD>綜合應用三天所學技能進行分組挑戰</TD>
            <TD></TD>
        </TR>
        <TR>
            <TD>13:30 - 15:00</TD>
            <TD>無痕山林與拔營</TD>
            <TD>環境復原、心得分享與頒發結業證書</TD>
            <TD></TD>
        </TR>
    </TABLE>

    <BR> 
    <P>建議攜帶物品：</P>
    <UL>
        <LI>個人急救藥品</LI> 
        <LI>不怕髒的衣物</LI> 
        <LI>防蚊液與手電筒</LI>
        <LI>健保卡</LI>
    </UL>

    <HR>

    <H2>報名表資訊</H2>
    <form action="result.php" method="POST">
        姓名: <input type="text" placeholder="您的姓名" name="nName" required><br/><br/>

        性別:
        男 <input type="radio" name="mGender" value="男">
        女 <input type="radio" name="mGender" value="女"><br/><br/>

        來自哪個城市：
        <select name="nCity">
            <option value="Taipei">台北市</option>
            <option value="NewTaipei">新北市</option>
            <option value="Keelung">基隆市</option>
            <option value="Taoyuan">桃園市</option>
            <option value="HsinchuC">新竹市</option>
            <option value="Hsinchu">新竹縣</option>
            <option value="Yilan">宜蘭縣</option>
            <option value="Miaoli">苗栗縣</option>
            <option value="Taichung" selected>台中市</option>
            <option value="Changhua">彰化縣</option>
            <option value="Nantou">南投縣</option>
            <option value="Yunlin">雲林縣</option>
            <option value="ChiayiC">嘉義市</option>
            <option value="Chiayi">嘉義縣</option>
            <option value="Tainan">台南市</option>
            <option value="Kaohsiung">高雄市</option>
            <option value="Pingtung">屏東縣</option>
            <option value="Hualien">花蓮縣</option>
            <option value="Taitung">台東縣</option>
            <option value="Penghu">澎湖縣</option>
            <option value="Kinmen">金門縣</option>
            <option value="Matsu">馬祖縣</option>
        </select>
        <br/><br/>

        感興趣的求生技能:
        取火 <input type="checkbox" name="mInterest[]" value="取火"> 
        辨識方位 <input type="checkbox" name="mInterest[]" value="方位"> 
        急救 <input type="checkbox" name="mInterest[]" value="急救">
        <br/><br/>

        聯絡 Email: <input type="email" name="mEmail" required><br/><br/>

        <input type="submit" value="送出報名">
        <input type="reset" value="重新填寫">
    </form>

</BODY>
</HTML>
