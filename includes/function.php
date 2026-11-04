<?php 

function random_string() {
    if(function_exists('random_bytes')) {
       $bytes = random_bytes(16);
       $str = bin2hex($bytes); 
    } else if(function_exists('openssl_random_pseudo_bytes')) {
       $bytes = openssl_random_pseudo_bytes(16);
       $str = bin2hex($bytes); 
    } else if(function_exists('mcrypt_create_iv')) {
       $bytes = mcrypt_create_iv(16, MCRYPT_DEV_URANDOM);
       $str = bin2hex($bytes); 
    } else {
       $str = md5(uniqid('sdfsffhg42!!§""fhgfdgdg3243542!!§""3"""§$"§', true));
    }   
    return $str;
}

function  log_acp($LogCategory,$LogText,$LogSupporterPid,$LogsupporterName,$LogSupporterGroup,$LogUserPid,$LogUserName,$LogShort = "N/A") {
   require ("includes/mysql.php");
   $stmt = $dbl->prepare("INSERT INTO acp_logs (category, text, supporter_pid, supporter_name, supporter_group, user_pid, user_name, short_note) VALUES (?,?,?,?,?,?,?,?)");
   $stmt->bind_param("ssssssss", $LogCategory,$LogText,$LogSupporterPid,$LogsupporterName,$LogSupporterGroup,$LogUserPid,$LogUserName,$LogShort);
   $stmt->execute();
}

function translate($translated) {
   $translated = str_ireplace("deaglegold_plan",'Bauplan: Deagle gold', $translated);
   $translated = str_ireplace("deagle_magazin_plan",'Bauplan: Deagle Magazin', $translated);
   $translated = str_ireplace("acr_remington_plan",'Bauplan: Acr Remington', $translated);
   $translated = str_ireplace("hk416_plan",'Bauplan: Hk416', $translated);
   $translated = str_ireplace("mk18_plan",'Bauplan: MK18', $translated);
   $translated = str_ireplace("SMG_03C_khaki_plan",'Bauplan: ADR-97C 5.7 mm (Khaki)', $translated);
   $translated = str_ireplace("arifle_TRG20_F_plan",'Bauplan: TRG-20 5.56 mm', $translated);
   $translated = str_ireplace("SMA_Steyr_AUG_F_plan",'Bauplan: Steyr AUG', $translated);
   $translated = str_ireplace("SMA_Tavor_F_plan",'Bauplan: Tavor 21', $translated);
   $translated = str_ireplace("m4a1_plan",'Bauplan: M4a1', $translated);
   $translated = str_ireplace("556_magazin_plan",'5.56 Magazin Bauplan', $translated);
   $translated = str_ireplace("57mm_magazin_plan",'Bauplan: 5.7 mm 50Rnd ADR-97', $translated);
   $translated = str_ireplace("sma_spitfire_01_black_plan",'Bauplan: Visier Hamr SMA Spitfire V1', $translated);
   $translated = str_ireplace("sma_spitfire_03_black_plan",'Bauplan: Visier Hamr SMA Spitfire V2', $translated);
   $translated = str_ireplace("optic_ERCO_khk_F_plan",'STR_Item_optic_ERCO_khk_F', $translated);
   $translated = str_ireplace("optic_ERCO_snd_F_plan",'STR_Item_optic_ERCO_snd_F', $translated);
   $translated = str_ireplace("ACE_optic_Hamr_2D_plan",'Bauplan: Leupold Mark 4 HAMR (2D)', $translated);
   $translated = str_ireplace("ACE_optic_Arco_2D_plan",'STR_Item_ACE_optic_Arco_2D_plan', $translated);
   $translated = str_ireplace("GR_Bell412_2_plan",'Bell 412', $translated);
   $translated = str_ireplace("B_Heli_Transport_03_unarmed_F_plan",'Chinook', $translated);
   $translated = str_ireplace("Sab_an12_mc_plan",'Antonow AN-12', $translated);
   $translated = str_ireplace("potato",'Kartoffel', $translated);
   $translated = str_ireplace("pretzel",'Brezel', $translated);
   $translated = str_ireplace("sandwich",'Sandwich', $translated);
   $translated = str_ireplace("baguette",'Belegtes Broetchen', $translated);
   $translated = str_ireplace("donuts",'Donuts', $translated);
   $translated = str_ireplace("frenchfries",'Pommes', $translated);
   $translated = str_ireplace("currywurst",'Currywurst', $translated);
   $translated = str_ireplace("hamburger",'Hamburger', $translated);
   $translated = str_ireplace("cheeseburger",'Cheeseburger', $translated);
   $translated = str_ireplace("thunfischbroetchen",'Thunfischburger', $translated);
   $translated = str_ireplace("item_d3s_RS6_20",'STR_d3s_RS6_20', $translated);
   $translated = str_ireplace("item_d3s_RS6_20_D",'Audi RS6', $translated);
   $translated = str_ireplace("item_d3s_RS6_20_SUN",'STR_d3s_RS6_20_SUN', $translated);
   $translated = str_ireplace("item_d3s_e60_09_ACS5",'STR_d3s_e60_09_ACS5', $translated);
   $translated = str_ireplace("item_d3s_amgGT_20_B",'STR_d3s_amgGT_20_B', $translated);
   $translated = str_ireplace("item_d3s_cherokee_18",'STR_d3s_cherokee_18', $translated);
   $translated = str_ireplace("item_d3s_lfa_10",'Lexus LF-A', $translated);
   $translated = str_ireplace("item_C_Heli_Light_01_civil_F",'MD 500', $translated);
   $translated = str_ireplace("item_B_Heli_Transport_03_unarmed_F",'Chinook', $translated);
   $translated = str_ireplace("item_GR_Bell412_2",'Bell 412', $translated);
   $translated = str_ireplace("item_Sab_an12_mc",'Antonow AN-12', $translated);
   $translated = str_ireplace("chassis",'Fahrwerk', $translated);
   $translated = str_ireplace("headlights",'Scheinwerfer', $translated);
   $translated = str_ireplace("window",'Scheibe', $translated);
   $translated = str_ireplace("engine",'Motor', $translated);
   $translated = str_ireplace("electric",'Elektrik', $translated);
   $translated = str_ireplace("carbody",'Karosserie', $translated);
   $translated = str_ireplace("vehchip",'Motorchip', $translated);
   $translated = str_ireplace("rotor",'Rotor', $translated);
   $translated = str_ireplace("v_RH_Deagleg",'Desert Eagle Gold', $translated);
   $translated = str_ireplace("v_RH_7Rnd_50_AE",'Desert Eagle Gold Magazin', $translated);
   $translated = str_ireplace("v_SMA_M4MOE_OD",'M4A1 Tactical OD SM(VFG)', $translated);
   $translated = str_ireplace("v_SMA_MK18MOEOD",'MK18 MOD1 OD(VFG)', $translated);
   $translated = str_ireplace("v_SMA_HK416MOEOD",'HK416 OD(AFG)', $translated);
   $translated = str_ireplace("v_SMA_ACRREMMOE",'ACR Remington (VFG)', $translated);
   $translated = str_ireplace("v_arifle_TRG20_F",'CTAR-21', $translated);
   $translated = str_ireplace("v_SMA_Steyr_AUG_F",'Steyr AUG', $translated);
   $translated = str_ireplace("v_SMA_Tavor_F",'Tavor 21', $translated);
   $translated = str_ireplace("v_hlc_30rnd_556x45_EPR_EMAG",'5.56mm EPR 30Rnd EMAG', $translated);
   $translated = str_ireplace("v_SMG_03C_khaki",'ADR-97C 5,7 mm (khaki)', $translated);
   $translated = str_ireplace("v_50Rnd_570x28_SMG_03",'50-Schuss-5.7mm-ADR-97-Magazin', $translated);
   $translated = str_ireplace("weaponpartsmall",'Waffenteil [Klein]', $translated);
   $translated = str_ireplace("weaponpartsmedium",'Waffenteil [Mittel]', $translated);
   $translated = str_ireplace("weaponpartslarge",'Waffenteil [Gross]', $translated);
   $translated = str_ireplace("small_ammonition",'Patronenhülsen (Klein)', $translated);
   $translated = str_ireplace("medium_ammonition",'Patronenhülsen (Mittel)', $translated);
   $translated = str_ireplace("large_ammonition",'Patronenhülsen (Groß)', $translated);
   $translated = str_ireplace("empty_magazine",'Leeres Magazin', $translated);
   $translated = str_ireplace("bullet_556",'Patrone 5.56mm', $translated);
   $translated = str_ireplace("bullet_50AE",'Patrone .50AE', $translated);
   $translated = str_ireplace("bullet_57",'Patrone 5.7mm', $translated);
   $translated = str_ireplace("backpack",'ICH BIN EIN RUCKSACK', $translated);
   $translated = str_ireplace("prisoner_backpack",'ICH BIN EIN RUCKSACK', $translated);
   $translated = str_ireplace("item_TRYK_B_Coyotebackpack_BLK",'schwarzer Rucksack', $translated);
   $translated = str_ireplace("item_Volunteers_Backpack_V1",'Rucksack [V]', $translated);
   $translated = str_ireplace("item_Rentner_Backpack_V1",'Rucksack [Rentner]', $translated);
   $translated = str_ireplace("Upgrade_trunk_1",'Kofferraum Upgrade [Lvl:1]', $translated);
   $translated = str_ireplace("Upgrade_trunk_2",'Kofferraum Upgrade [Lvl:2]', $translated);
   $translated = str_ireplace("Upgrade_trunk_3",'Kofferraum Upgrade [Lvl:3]', $translated);
   $translated = str_ireplace("v_sma_spitfire_01_black",'Visier SMA Spitfire V1', $translated);
   $translated = str_ireplace("v_sma_spitfire_03_black",'Visier SMA Spitfire V2', $translated);
   $translated = str_ireplace("v_optic_ERCO_khk_F",'ERCO (Khaki)', $translated);
   $translated = str_ireplace("v_optic_ERCO_snd_F",'ERCO (Sand)', $translated);
   $translated = str_ireplace("v_ACE_optic_Hamr_2D",'Leupold Mark 4 HAMR (2D)', $translated);
   $translated = str_ireplace("v_ACE_optic_Arco_2D",'ELCAN SpecterOS (2D)', $translated);
   $translated = str_ireplace("tp_fakeCard",'Diesel', $translated);
   $translated = str_ireplace("diesel",'Diesel', $translated);
   $translated = str_ireplace("benzin",'Benzin', $translated);
   $translated = str_ireplace("kerosin",'Kerosin', $translated);
   $translated = str_ireplace("sealwarrent",'Versiegelungsbeschluss', $translated);
   $translated = str_ireplace("chest",'Kiste', $translated);
   $translated = str_ireplace("prisoner_male",'Prisoner Mann', $translated);
   $translated = str_ireplace("prisoner_female",'Prisoner Frau', $translated);
   $translated = str_ireplace("vtoolkit",'Werkzeugkit', $translated);
   $translated = str_ireplace("fuelEmpty",'Leerer Benzinkanister', $translated);
   $translated = str_ireplace("evidencebag",'Beweistasche', $translated);
   $translated = str_ireplace("garbagebag",'Mülltüte', $translated);
   $translated = str_ireplace("fuelFull",'Gefüllter Benzinkanister', $translated);
   $translated = str_ireplace("spikeStrip",'Nagelband', $translated);
   $translated = str_ireplace("housesecurityupgrade",'Security System', $translated);
   $translated = str_ireplace("screwdriver",'Schraubendreher', $translated);
   $translated = str_ireplace("lockpick",'Dietrich', $translated);
   $translated = str_ireplace("tire",'Ersatzreifen', $translated);
   $translated = str_ireplace("goldbar",'Goldbarren', $translated);
   $translated = str_ireplace("boltcutter",'Bolzenschneider', $translated);
   $translated = str_ireplace("storagesmall",'Kleine Lagerbox', $translated);
   $translated = str_ireplace("storagebig",'Große Lagerbox', $translated);
   $translated = str_ireplace("storagebig_1",'Große Lagerbox 1k', $translated);
   $translated = str_ireplace("storagebig_2",'Große Lagerbox 2k', $translated);
   $translated = str_ireplace("storagebig_3",'Große Lagerbox 4k', $translated);
   $translated = str_ireplace("storagebig_4",'Große Lagerbox 10k', $translated);
   $translated = str_ireplace("storagebig_5",'Große Lagerbox 20k', $translated);
   $translated = str_ireplace("storagebig_6",'Große Lagerbox 30k', $translated);
   $translated = str_ireplace("storagebig_7",'Große Lagerbox 40k', $translated);
   $translated = str_ireplace("storagebig_8",'Große Lagerbox 50k', $translated);
   $translated = str_ireplace("storageupgrade",'Lagerbox upgrade', $translated);
   $translated = str_ireplace("speedcam",'Radarkamera', $translated);
   $translated = str_ireplace("emergencyhammer",'Nothammer', $translated);
   $translated = str_ireplace("redgull",'28 Black', $translated);
   $translated = str_ireplace("coffee",'Kaffee', $translated);
   $translated = str_ireplace("chocolatemilk",'Schokomilch', $translated);
   $translated = str_ireplace("waterBottle",'Wasserflasche', $translated);
   $translated = str_ireplace("colorbucket",'STR_Item_colorbucket', $translated);
   $translated = str_ireplace("fanta",'Fanta', $translated);
   $translated = str_ireplace("cola",'Cola', $translated);
   $translated = str_ireplace("sprite",'Sprite', $translated);
   $translated = str_ireplace("caprisonne",'Capri-Sonne', $translated);
   $translated = str_ireplace("champagner",'Champagner', $translated);
   $translated = str_ireplace("caipirinha",'Caipirinha', $translated);
   $translated = str_ireplace("red_russian",'Red Russian', $translated);
   $translated = str_ireplace("mojito",'Mojito', $translated);
   $translated = str_ireplace("mai_tai",'Mai Tai', $translated);
   $translated = str_ireplace("red_lady",'Red Lady', $translated);
   $translated = str_ireplace("pina_colada",'Pina Colada', $translated);
   $translated = str_ireplace("magarita",'Magarita', $translated);
   $translated = str_ireplace("moscow_mule",'Moscow Mule', $translated);
   $translated = str_ireplace("tequila_sunrise",'Tequila Sunrise', $translated);
   $translated = str_ireplace("sex_on_the_beach",'Sex on the Beach', $translated);
   $translated = str_ireplace("bloody_mary",'Bloody Mary', $translated);
   $translated = str_ireplace("gin_tonic",'Gin Tonic', $translated);
   $translated = str_ireplace("beer",'Bier', $translated);
   $translated = str_ireplace("vodka",'Vodka', $translated);
   $translated = str_ireplace("whiskey",'Whiskey', $translated);
   $translated = str_ireplace("icetealemon",'Eistee: Zitrone', $translated);
   $translated = str_ireplace("iceteapeach",'Eistee: Pfirsich', $translated);
   $translated = str_ireplace("cherrywater",'Active O2 Kirsche', $translated);
   $translated = str_ireplace("big_win",'Big Win', $translated);
   $translated = str_ireplace("lucky_jackpot",'Lucky Jackpot', $translated);
   $translated = str_ireplace("fast_money",'Fast Money', $translated);
   $translated = str_ireplace("weddingring",'Ehering', $translated);
   $translated = str_ireplace("engagementring",'Verlobungsring', $translated);
   $translated = str_ireplace("Gluhwein",'Glühwein', $translated);
   $translated = str_ireplace("cookie",'Kekse', $translated);
   $translated = str_ireplace("gingerbread",'Lebkuchen', $translated);
   $translated = str_ireplace("Praline",'Praline', $translated);
   $translated = str_ireplace("chocolate",'Schokolade', $translated);
   $translated = str_ireplace("Rose",'Rose', $translated);
   $translated = str_ireplace("voucher_100k",'Gutschein $100k', $translated);
   $translated = str_ireplace("tbacon",'Taktischer Speck', $translated);
   $translated = str_ireplace("turtle_raw",'Schildkröte', $translated);
   $translated = str_ireplace("fish_raw",'Roher Fisch', $translated);
   $translated = str_ireplace("fish",'Fischfilet', $translated);
   $translated = str_ireplace("seed_cannabis",'Cannabissamen', $translated);
   $translated = str_ireplace("seed_kokain",'Kokainsamen', $translated);
   $translated = str_ireplace("seed_opium",'Opiumsamen', $translated);
   $translated = str_ireplace("weedkiller",'Weedkiller', $translated);
   $translated = str_ireplace("wateringcan",'Gießkanne', $translated);
   $translated = str_ireplace("blackmoney",'Schwarzgeld', $translated);
   $translated = str_ireplace("apple",'Apfel', $translated);
   $translated = str_ireplace("applejuice",'Apfelsaft', $translated);
   $translated = str_ireplace("grapejuice",'Traubensaft', $translated);
   $translated = str_ireplace("garbage",'Muell', $translated);
   $translated = str_ireplace("plastic",'Plastik', $translated);
   $translated = str_ireplace("copperore",'Kupfererz', $translated);
   $translated = str_ireplace("copperingot",'Kupferbarren', $translated);
   $translated = str_ireplace("zincore",'Zinkerz', $translated);
   $translated = str_ireplace("zincingot",'Zinkbarren', $translated);
   $translated = str_ireplace("brassingot",'Messingbarren', $translated);
   $translated = str_ireplace("ironore",'Eisenerz', $translated);
   $translated = str_ireplace("ironingot",'Eisenbarre(n)', $translated);
   $translated = str_ireplace("silverore",'Silbererz', $translated);
   $translated = str_ireplace("silveringot",'Silberbarren', $translated);
   $translated = str_ireplace("sand",'Sand', $translated);
   $translated = str_ireplace("glas",'Glas', $translated);
   $translated = str_ireplace("saltcrystal",'Salz', $translated);
   $translated = str_ireplace("salt",'Raffiniertes Salz', $translated);
   $translated = str_ireplace("diamondore",'Rohdiamanten', $translated);
   $translated = str_ireplace("diamondcut",'Geschliffene Diamanten', $translated);
   $translated = str_ireplace("coalore",'Kohlegestein', $translated);
   $translated = str_ireplace("coal",'Kohle', $translated);
   $translated = str_ireplace("stone",'Stein', $translated);
   $translated = str_ireplace("brick",'Ziegelstein', $translated);
   $translated = str_ireplace("cotton",'Baumwolle', $translated);
   $translated = str_ireplace("rawrubber",'Kautschuk', $translated);
   $translated = str_ireplace("rubber",'Gummi', $translated);
   $translated = str_ireplace("kevlar",'Kevlar', $translated);
   $translated = str_ireplace("turtleshell",'Schildkröten-Panzer', $translated);
   $translated = str_ireplace("fabric",'Stoff', $translated);
   $translated = str_ireplace("clothes",'Kleidung', $translated);
   $translated = str_ireplace("cement",'Zement', $translated);
   $translated = str_ireplace("clay",'Lehm', $translated);
   $translated = str_ireplace("oil",'Rohöl', $translated);
   $translated = str_ireplace("oilbarrel",'Raffiniertes Öl', $translated);
   $translated = str_ireplace("wheat",'Weizen', $translated);
   $translated = str_ireplace("flour",'Mehl', $translated);
   $translated = str_ireplace("applecorn",'Apfelkorn', $translated);
   $translated = str_ireplace("redwine",'Rotwein', $translated);
   $translated = str_ireplace("grape",'Traube', $translated);
   $translated = str_ireplace("cannabis",'Cannabisblatt', $translated);
   $translated = str_ireplace("cannabisbag",'Cannabis Beutel', $translated);
   $translated = str_ireplace("opium",'Opium', $translated);
   $translated = str_ireplace("heroin",'Heroin Spritze', $translated);
   $translated = str_ireplace("cocaine",'Kokapflanze', $translated);
   $translated = str_ireplace("cocainbag",'Kokainbeutel', $translated);
   $translated = str_ireplace("gunpowder",'Schiesspulver', $translated);
   $translated = str_ireplace("nitro",'Nitro', $translated);
   $translated = str_ireplace("mushroom",'Pilz', $translated);
   $translated = str_ireplace("magicmushroom",'Magic Mushroom', $translated);
   $translated = str_ireplace("steelingot",'Stahlbarren', $translated);
   $translated = str_ireplace("woodenlog",'Holz', $translated);
   $translated = str_ireplace("woodenboard",'Holzbrett', $translated);
   $translated = str_ireplace("furniture",'Möbel', $translated);
   $translated = str_ireplace("drugkitchen_small",'Drogenküche klein', $translated);
   $translated = str_ireplace("drugkitchen_medium",'Drogenküche mittel', $translated);
   $translated = str_ireplace("drugkitchen_large",'Drogenküche groß', $translated);
   $translated = str_ireplace("package",'Paket', $translated);
   $translated = str_ireplace("gpstracker",'GPS Tracker', $translated);
   $translated = str_ireplace("cuttingpliers",'Drahtschneider', $translated);
   $translated = str_ireplace("teddy",'Teddy', $translated);

   return $translated;
}

function translate2($translated) {

   $translated = str_ireplace("`", '"', $translated);
   return $translated;
}

function translate3($translated) {

   $translated = str_ireplace("1", 'Ja', $translated);
   $translated = str_ireplace("0", 'Nein', $translated);
   return $translated;
}

function translate4($translated) {
   $translated = str_ireplace("ü", 'ue', $translated);
   $translated = str_ireplace("ä", 'ae', $translated);
   $translated = str_ireplace("ö", 'oe', $translated);
   $translated = str_ireplace("ß", 'ss', $translated);

   return $translated;
}

function translate555($translated) {

   $translated = str_ireplace("`", '', $translated);
   return $translated;
}

function translateMoneyHouses($translated) {
   $translated = str_ireplace("Land_i_House_Big_02_V1_F", '22500000', $translated);
   $translated = str_ireplace("Land_i_House_Big_02_V2_F", '22500000', $translated);
   $translated = str_ireplace("Land_i_House_Big_02_V3_F", '22500000', $translated);
   $translated = str_ireplace("Land_i_House_Big_01_V1_F", '30000000', $translated);
   $translated = str_ireplace("Land_i_House_Big_01_V2_F", '30000000', $translated);
   $translated = str_ireplace("Land_i_House_Big_01_V3_F", '30000000', $translated);
   $translated = str_ireplace("Land_i_House_Small_01_V1_F", '15000000', $translated);
   $translated = str_ireplace("Land_i_House_Small_01_V2_F", '15000000', $translated);
   $translated = str_ireplace("Land_i_House_Small_01_V3_F", '15000000', $translated);
   $translated = str_ireplace("Land_i_House_Small_02_V1_F", '15000000', $translated);
   $translated = str_ireplace("Land_i_House_Small_02_V2_F", '15000000', $translated);
   $translated = str_ireplace("Land_i_House_Small_02_V3_F", '15000000', $translated);
   $translated = str_ireplace("Land_i_House_Small_03_V1_F", '22500000', $translated);
   $translated = str_ireplace("Land_i_House_Small_03_V3_F", '22500000', $translated);
   $translated = str_ireplace("Land_i_Stone_HouseSmall_V1_F", '8000000', $translated);
   $translated = str_ireplace("Land_i_Garage_V1_F", '8000000', $translated);
   $translated = str_ireplace("Land_i_Garage_V2_F", '8000000', $translated);
   $translated = str_ireplace("Land_i_Stone_HouseSmall_V2_F", '8000000', $translated);
   $translated = str_ireplace("Land_i_Stone_HouseSmall_V3_F", '8000000', $translated);
   $translated = str_ireplace("Land_i_Shed_Ind_F", '70000000', $translated);
   
   return $translated;
}

function translateVehicles($translated) {
   $translated = str_ireplace("d3s_4xxMANS_16", 'Mansory Siracusa 4XX16', $translated);
   $translated = str_ireplace("d3s_4xxMANS_16_S", 'Mansory Siracusa 4XX Spider 16', $translated);
   $translated = str_ireplace("d3s_200_16", 'Toyota Land Cruiser 200 16', $translated);
   $translated = str_ireplace("d3s_200_16_EX", 'Toyota Land Cruiser 200 16 [Exclusive]', $translated);
   $translated = str_ireplace("d3s_200_16_Police", 'Toyota Land Cruiser 200 16 [Police]', $translated);
   $translated = str_ireplace("d3s_200_16_Medical", 'Toyota Land Cruiser 200 16 [Medical]', $translated);

   return $translated;
}

function translateImpound($translated) {
   $translated = str_ireplace("0", 'Nein - Stufe 0', $translated);
   $translated = str_ireplace("1", 'Ja - Stufe 1', $translated);
   $translated = str_ireplace("2", 'Ja - Stufe 2', $translated);
   $translated = str_ireplace("3", 'Ja - Stufe 3', $translated);
   $translated = str_ireplace("4", 'Ja - Stufe 4', $translated);

   return $translated;
}

function translateVItems($translated) {
   $translated = str_ireplace("deaglegold_plan",'Bauplan: Deagle gold', $translated);
   $translated = str_ireplace("deagle_magazin_plan",'Bauplan: Deagle Magazin', $translated);
   $translated = str_ireplace("acr_remington_plan",'Bauplan: Acr Remington', $translated);
   $translated = str_ireplace("hk416_plan",'Bauplan: Hk416', $translated);
   $translated = str_ireplace("mk18_plan",'Bauplan: MK18', $translated);
   $translated = str_ireplace("SMG_03C_khaki_plan",'Bauplan: ADR-97C 5.7 mm (Khaki)', $translated);
   $translated = str_ireplace("arifle_TRG20_F_plan",'Bauplan: TRG-20 5.56 mm', $translated);
   $translated = str_ireplace("SMA_Steyr_AUG_F_plan",'Bauplan: Steyr AUG', $translated);
   $translated = str_ireplace("SMA_Tavor_F_plan",'Bauplan: Tavor 21', $translated);
   $translated = str_ireplace("m4a1_plan",'Bauplan: M4a1', $translated);
   $translated = str_ireplace("556_magazin_plan",'5.56 Magazin Bauplan', $translated);
   $translated = str_ireplace("57mm_magazin_plan",'Bauplan: 5.7 mm 50Rnd ADR-97', $translated);
   $translated = str_ireplace("sma_spitfire_01_black_plan",'Bauplan: Visier Hamr SMA Spitfire V1', $translated);
   $translated = str_ireplace("sma_spitfire_03_black_plan",'Bauplan: Visier Hamr SMA Spitfire V2', $translated);
   $translated = str_ireplace("optic_ERCO_khk_F_plan",'STR_Item_optic_ERCO_khk_F', $translated);
   $translated = str_ireplace("optic_ERCO_snd_F_plan",'STR_Item_optic_ERCO_snd_F', $translated);
   $translated = str_ireplace("ACE_optic_Hamr_2D_plan",'Bauplan: Leupold Mark 4 HAMR (2D)', $translated);
   $translated = str_ireplace("ACE_optic_Arco_2D_plan",'STR_Item_ACE_optic_Arco_2D_plan', $translated);
   $translated = str_ireplace("GR_Bell412_2_plan",'Bell 412', $translated);
   $translated = str_ireplace("B_Heli_Transport_03_unarmed_F_plan",'Chinook', $translated);
   $translated = str_ireplace("Sab_an12_mc_plan",'Antonow AN-12', $translated);
   $translated = str_ireplace("potato",'Kartoffel', $translated);
   $translated = str_ireplace("pretzel",'Brezel', $translated);
   $translated = str_ireplace("sandwich",'Sandwich', $translated);
   $translated = str_ireplace("baguette",'Belegtes Broetchen', $translated);
   $translated = str_ireplace("donuts",'Donuts', $translated);
   $translated = str_ireplace("frenchfries",'Pommes', $translated);
   $translated = str_ireplace("currywurst",'Currywurst', $translated);
   $translated = str_ireplace("hamburger",'Hamburger', $translated);
   $translated = str_ireplace("cheeseburger",'Cheeseburger', $translated);
   $translated = str_ireplace("thunfischbroetchen",'Thunfischburger', $translated);
   $translated = str_ireplace("item_d3s_RS6_20",'STR_d3s_RS6_20', $translated);
   $translated = str_ireplace("item_d3s_RS6_20_D",'Audi RS6', $translated);
   $translated = str_ireplace("item_d3s_RS6_20_SUN",'STR_d3s_RS6_20_SUN', $translated);
   $translated = str_ireplace("item_d3s_e60_09_ACS5",'STR_d3s_e60_09_ACS5', $translated);
   $translated = str_ireplace("item_d3s_amgGT_20_B",'STR_d3s_amgGT_20_B', $translated);
   $translated = str_ireplace("item_d3s_cherokee_18",'STR_d3s_cherokee_18', $translated);
   $translated = str_ireplace("item_d3s_lfa_10",'Lexus LF-A', $translated);
   $translated = str_ireplace("item_C_Heli_Light_01_civil_F",'MD 500', $translated);
   $translated = str_ireplace("item_B_Heli_Transport_03_unarmed_F",'Chinook', $translated);
   $translated = str_ireplace("item_GR_Bell412_2",'Bell 412', $translated);
   $translated = str_ireplace("item_Sab_an12_mc",'Antonow AN-12', $translated);
   $translated = str_ireplace("chassis",'Fahrwerk', $translated);
   $translated = str_ireplace("headlights",'Scheinwerfer', $translated);
   $translated = str_ireplace("window",'Scheibe', $translated);
   $translated = str_ireplace("engine",'Motor', $translated);
   $translated = str_ireplace("electric",'Elektrik', $translated);
   $translated = str_ireplace("carbody",'Karosserie', $translated);
   $translated = str_ireplace("vehchip",'Motorchip', $translated);
   $translated = str_ireplace("rotor",'Rotor', $translated);
   $translated = str_ireplace("v_RH_Deagleg",'Desert Eagle Gold', $translated);
   $translated = str_ireplace("v_RH_7Rnd_50_AE",'Desert Eagle Gold Magazin', $translated);
   $translated = str_ireplace("v_SMA_M4MOE_OD",'M4A1 Tactical OD SM(VFG)', $translated);
   $translated = str_ireplace("v_SMA_MK18MOEOD",'MK18 MOD1 OD(VFG)', $translated);
   $translated = str_ireplace("v_SMA_HK416MOEOD",'HK416 OD(AFG)', $translated);
   $translated = str_ireplace("v_SMA_ACRREMMOE",'ACR Remington (VFG)', $translated);
   $translated = str_ireplace("v_arifle_TRG20_F",'CTAR-21', $translated);
   $translated = str_ireplace("v_SMA_Steyr_AUG_F",'Steyr AUG', $translated);
   $translated = str_ireplace("v_SMA_Tavor_F",'Tavor 21', $translated);
   $translated = str_ireplace("v_hlc_30rnd_556x45_EPR_EMAG",'5.56mm EPR 30Rnd EMAG', $translated);
   $translated = str_ireplace("v_SMG_03C_khaki",'ADR-97C 5,7 mm (khaki)', $translated);
   $translated = str_ireplace("v_50Rnd_570x28_SMG_03",'50-Schuss-5.7mm-ADR-97-Magazin', $translated);
   $translated = str_ireplace("weaponpartsmall",'Waffenteil [Klein]', $translated);
   $translated = str_ireplace("weaponpartsmedium",'Waffenteil [Mittel]', $translated);
   $translated = str_ireplace("weaponpartslarge",'Waffenteil [Gross]', $translated);
   $translated = str_ireplace("small_ammonition",'Patronenhülsen (Klein)', $translated);
   $translated = str_ireplace("medium_ammonition",'Patronenhülsen (Mittel)', $translated);
   $translated = str_ireplace("large_ammonition",'Patronenhülsen (Groß)', $translated);
   $translated = str_ireplace("empty_magazine",'Leeres Magazin', $translated);
   $translated = str_ireplace("bullet_556",'Patrone 5.56mm', $translated);
   $translated = str_ireplace("bullet_50AE",'Patrone .50AE', $translated);
   $translated = str_ireplace("bullet_57",'Patrone 5.7mm', $translated);
   $translated = str_ireplace("backpack",'ICH BIN EIN RUCKSACK', $translated);
   $translated = str_ireplace("prisoner_backpack",'ICH BIN EIN RUCKSACK', $translated);
   $translated = str_ireplace("item_TRYK_B_Coyotebackpack_BLK",'schwarzer Rucksack', $translated);
   $translated = str_ireplace("item_Volunteers_Backpack_V1",'Rucksack [V]', $translated);
   $translated = str_ireplace("item_Rentner_Backpack_V1",'Rucksack [Rentner]', $translated);
   $translated = str_ireplace("Upgrade_trunk_1",'Kofferraum Upgrade [Lvl:1]', $translated);
   $translated = str_ireplace("Upgrade_trunk_2",'Kofferraum Upgrade [Lvl:2]', $translated);
   $translated = str_ireplace("Upgrade_trunk_3",'Kofferraum Upgrade [Lvl:3]', $translated);
   $translated = str_ireplace("v_sma_spitfire_01_black",'Visier SMA Spitfire V1', $translated);
   $translated = str_ireplace("v_sma_spitfire_03_black",'Visier SMA Spitfire V2', $translated);
   $translated = str_ireplace("v_optic_ERCO_khk_F",'ERCO (Khaki)', $translated);
   $translated = str_ireplace("v_optic_ERCO_snd_F",'ERCO (Sand)', $translated);
   $translated = str_ireplace("v_ACE_optic_Hamr_2D",'Leupold Mark 4 HAMR (2D)', $translated);
   $translated = str_ireplace("v_ACE_optic_Arco_2D",'ELCAN SpecterOS (2D)', $translated);
   $translated = str_ireplace("tp_fakeCard",'Diesel', $translated);
   $translated = str_ireplace("diesel",'Diesel', $translated);
   $translated = str_ireplace("benzin",'Benzin', $translated);
   $translated = str_ireplace("kerosin",'Kerosin', $translated);
   $translated = str_ireplace("sealwarrent",'Versiegelungsbeschluss', $translated);
   $translated = str_ireplace("chest",'Kiste', $translated);
   $translated = str_ireplace("prisoner_male",'Prisoner Mann', $translated);
   $translated = str_ireplace("prisoner_female",'Prisoner Frau', $translated);
   $translated = str_ireplace("vtoolkit",'Werkzeugkit', $translated);
   $translated = str_ireplace("fuelEmpty",'Leerer Benzinkanister', $translated);
   $translated = str_ireplace("evidencebag",'Beweistasche', $translated);
   $translated = str_ireplace("garbagebag",'Mülltüte', $translated);
   $translated = str_ireplace("fuelFull",'Gefüllter Benzinkanister', $translated);
   $translated = str_ireplace("spikeStrip",'Nagelband', $translated);
   $translated = str_ireplace("housesecurityupgrade",'Security System', $translated);
   $translated = str_ireplace("screwdriver",'Schraubendreher', $translated);
   $translated = str_ireplace("lockpick",'Dietrich', $translated);
   $translated = str_ireplace("tire",'Ersatzreifen', $translated);
   $translated = str_ireplace("goldbar",'Goldbarren', $translated);
   $translated = str_ireplace("boltcutter",'Bolzenschneider', $translated);
   $translated = str_ireplace("storagesmall",'Kleine Lagerbox', $translated);
   $translated = str_ireplace("storagebig",'Große Lagerbox', $translated);
   $translated = str_ireplace("storagebig_1",'Große Lagerbox 1k', $translated);
   $translated = str_ireplace("storagebig_2",'Große Lagerbox 2k', $translated);
   $translated = str_ireplace("storagebig_3",'Große Lagerbox 4k', $translated);
   $translated = str_ireplace("storagebig_4",'Große Lagerbox 10k', $translated);
   $translated = str_ireplace("storagebig_5",'Große Lagerbox 20k', $translated);
   $translated = str_ireplace("storagebig_6",'Große Lagerbox 30k', $translated);
   $translated = str_ireplace("storagebig_7",'Große Lagerbox 40k', $translated);
   $translated = str_ireplace("storagebig_8",'Große Lagerbox 50k', $translated);
   $translated = str_ireplace("storageupgrade",'Lagerbox upgrade', $translated);
   $translated = str_ireplace("speedcam",'Radarkamera', $translated);
   $translated = str_ireplace("emergencyhammer",'Nothammer', $translated);
   $translated = str_ireplace("redgull",'28 Black', $translated);
   $translated = str_ireplace("coffee",'Kaffee', $translated);
   $translated = str_ireplace("chocolatemilk",'Schokomilch', $translated);
   $translated = str_ireplace("waterBottle",'Wasserflasche', $translated);
   $translated = str_ireplace("colorbucket",'STR_Item_colorbucket', $translated);
   $translated = str_ireplace("fanta",'Fanta', $translated);
   $translated = str_ireplace("cola",'Cola', $translated);
   $translated = str_ireplace("sprite",'Sprite', $translated);
   $translated = str_ireplace("caprisonne",'Capri-Sonne', $translated);
   $translated = str_ireplace("champagner",'Champagner', $translated);
   $translated = str_ireplace("caipirinha",'Caipirinha', $translated);
   $translated = str_ireplace("red_russian",'Red Russian', $translated);
   $translated = str_ireplace("mojito",'Mojito', $translated);
   $translated = str_ireplace("mai_tai",'Mai Tai', $translated);
   $translated = str_ireplace("red_lady",'Red Lady', $translated);
   $translated = str_ireplace("pina_colada",'Pina Colada', $translated);
   $translated = str_ireplace("magarita",'Magarita', $translated);
   $translated = str_ireplace("moscow_mule",'Moscow Mule', $translated);
   $translated = str_ireplace("tequila_sunrise",'Tequila Sunrise', $translated);
   $translated = str_ireplace("sex_on_the_beach",'Sex on the Beach', $translated);
   $translated = str_ireplace("bloody_mary",'Bloody Mary', $translated);
   $translated = str_ireplace("gin_tonic",'Gin Tonic', $translated);
   $translated = str_ireplace("beer",'Bier', $translated);
   $translated = str_ireplace("vodka",'Vodka', $translated);
   $translated = str_ireplace("whiskey",'Whiskey', $translated);
   $translated = str_ireplace("icetealemon",'Eistee: Zitrone', $translated);
   $translated = str_ireplace("iceteapeach",'Eistee: Pfirsich', $translated);
   $translated = str_ireplace("cherrywater",'Active O2 Kirsche', $translated);
   $translated = str_ireplace("big_win",'Big Win', $translated);
   $translated = str_ireplace("lucky_jackpot",'Lucky Jackpot', $translated);
   $translated = str_ireplace("fast_money",'Fast Money', $translated);
   $translated = str_ireplace("weddingring",'Ehering', $translated);
   $translated = str_ireplace("engagementring",'Verlobungsring', $translated);
   $translated = str_ireplace("Gluhwein",'Glühwein', $translated);
   $translated = str_ireplace("cookie",'Kekse', $translated);
   $translated = str_ireplace("gingerbread",'Lebkuchen', $translated);
   $translated = str_ireplace("Praline",'Praline', $translated);
   $translated = str_ireplace("chocolate",'Schokolade', $translated);
   $translated = str_ireplace("Rose",'Rose', $translated);
   $translated = str_ireplace("voucher_100k",'Gutschein $100k', $translated);
   $translated = str_ireplace("tbacon",'Taktischer Speck', $translated);
   $translated = str_ireplace("turtle_raw",'Schildkröte', $translated);
   $translated = str_ireplace("fish_raw",'Roher Fisch', $translated);
   $translated = str_ireplace("fish",'Fischfilet', $translated);
   $translated = str_ireplace("seed_cannabis",'Cannabissamen', $translated);
   $translated = str_ireplace("seed_kokain",'Kokainsamen', $translated);
   $translated = str_ireplace("seed_opium",'Opiumsamen', $translated);
   $translated = str_ireplace("weedkiller",'Weedkiller', $translated);
   $translated = str_ireplace("wateringcan",'Gießkanne', $translated);
   $translated = str_ireplace("blackmoney",'Schwarzgeld', $translated);
   $translated = str_ireplace("apple",'Apfel', $translated);
   $translated = str_ireplace("applejuice",'Apfelsaft', $translated);
   $translated = str_ireplace("grapejuice",'Traubensaft', $translated);
   $translated = str_ireplace("garbage",'Muell', $translated);
   $translated = str_ireplace("plastic",'Plastik', $translated);
   $translated = str_ireplace("copperore",'Kupfererz', $translated);
   $translated = str_ireplace("copperingot",'Kupferbarren', $translated);
   $translated = str_ireplace("zincore",'Zinkerz', $translated);
   $translated = str_ireplace("zincingot",'Zinkbarren', $translated);
   $translated = str_ireplace("brassingot",'Messingbarren', $translated);
   $translated = str_ireplace("ironore",'Eisenerz', $translated);
   $translated = str_ireplace("ironingot",'Eisenbarre(n)', $translated);
   $translated = str_ireplace("silverore",'Silbererz', $translated);
   $translated = str_ireplace("silveringot",'Silberbarren', $translated);
   $translated = str_ireplace("sand",'Sand', $translated);
   $translated = str_ireplace("glas",'Glas', $translated);
   $translated = str_ireplace("saltcrystal",'Salz', $translated);
   $translated = str_ireplace("salt",'Raffiniertes Salz', $translated);
   $translated = str_ireplace("diamondore",'Rohdiamanten', $translated);
   $translated = str_ireplace("diamondcut",'Geschliffene Diamanten', $translated);
   $translated = str_ireplace("coalore",'Kohlegestein', $translated);
   $translated = str_ireplace("coal",'Kohle', $translated);
   $translated = str_ireplace("stone",'Stein', $translated);
   $translated = str_ireplace("brick",'Ziegelstein', $translated);
   $translated = str_ireplace("cotton",'Baumwolle', $translated);
   $translated = str_ireplace("rawrubber",'Kautschuk', $translated);
   $translated = str_ireplace("rubber",'Gummi', $translated);
   $translated = str_ireplace("kevlar",'Kevlar', $translated);
   $translated = str_ireplace("turtleshell",'Schildkröten-Panzer', $translated);
   $translated = str_ireplace("fabric",'Stoff', $translated);
   $translated = str_ireplace("clothes",'Kleidung', $translated);
   $translated = str_ireplace("cement",'Zement', $translated);
   $translated = str_ireplace("clay",'Lehm', $translated);
   $translated = str_ireplace("oil",'Rohöl', $translated);
   $translated = str_ireplace("oilbarrel",'Raffiniertes Öl', $translated);
   $translated = str_ireplace("wheat",'Weizen', $translated);
   $translated = str_ireplace("flour",'Mehl', $translated);
   $translated = str_ireplace("applecorn",'Apfelkorn', $translated);
   $translated = str_ireplace("redwine",'Rotwein', $translated);
   $translated = str_ireplace("grape",'Traube', $translated);
   $translated = str_ireplace("cannabis",'Cannabisblatt', $translated);
   $translated = str_ireplace("cannabisbag",'Cannabis Beutel', $translated);
   $translated = str_ireplace("opium",'Opium', $translated);
   $translated = str_ireplace("heroin",'Heroin Spritze', $translated);
   $translated = str_ireplace("cocaine",'Kokapflanze', $translated);
   $translated = str_ireplace("cocainbag",'Kokainbeutel', $translated);
   $translated = str_ireplace("gunpowder",'Schiesspulver', $translated);
   $translated = str_ireplace("nitro",'Nitro', $translated);
   $translated = str_ireplace("mushroom",'Pilz', $translated);
   $translated = str_ireplace("magicmushroom",'Magic Mushroom', $translated);
   $translated = str_ireplace("steelingot",'Stahlbarren', $translated);
   $translated = str_ireplace("woodenlog",'Holz', $translated);
   $translated = str_ireplace("woodenboard",'Holzbrett', $translated);
   $translated = str_ireplace("furniture",'Möbel', $translated);
   $translated = str_ireplace("drugkitchen_small",'Drogenküche klein', $translated);
   $translated = str_ireplace("drugkitchen_medium",'Drogenküche mittel', $translated);
   $translated = str_ireplace("drugkitchen_large",'Drogenküche groß', $translated);
   $translated = str_ireplace("package",'Paket', $translated);
   $translated = str_ireplace("gpstracker",'GPS Tracker', $translated);
   $translated = str_ireplace("cuttingpliers",'Drahtschneider', $translated);
   $translated = str_ireplace("teddy",'Teddy', $translated);

   return $translated;
}

$beBanCheckSocket = NULL;
function beBanCheck($guid) {
    $server = gethostbyname('arma31.battleye.com');
    $port = 2344;
    global $beBanCheckSocket;
    if (is_null($beBanCheckSocket)) {
        if(!($beBanCheckSocket = socket_create(AF_INET, SOCK_DGRAM, 0))) {
            $errorcode = socket_last_error();
            $errormsg = socket_strerror($errorcode);        
            throw new Exception("Couldn't create socket: [$errorcode] $errormsg");
        }
    }
    $sock = $beBanCheckSocket;   

    $msg = "..6.." . $guid;
    if( ! socket_sendto($sock, $msg , strlen($msg) , 0 , $server , $port)) {
        $errorcode = socket_last_error();
        $errormsg = socket_strerror($errorcode);        
        throw new Exception("Could not send data: [$errorcode] $errormsg");
    }
        
    if(socket_recvfrom ( $sock , $reply , 2045 , 0, $host, $port  ) === FALSE)  {
        $errorcode = socket_last_error();
        $errormsg = socket_strerror($errorcode);        
        throw new Exception("Could not receive data: [$errorcode] $errormsg");
    }

    if ($reply === "..6.") return false;
    $replyBanned = "..6.Global Ban #" . substr($guid, 0, 6);
    if ($reply === $replyBanned ) return true;

    throw new Exception("Malformed BattlEye response: '$reply'. Expexted '$replyBanned' or '..6.'");
}
?>