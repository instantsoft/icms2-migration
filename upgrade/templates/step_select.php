<h1>Выбор контента</h1>

<p>Выберите контент который вы хотите перенести</p>

<form id="step-form">

        <div class="field module-content">
            <label>
				<input type="checkbox" class="module" data-module="content" checked="checked" value="1"> 
				<strong>Статьи <sup><?php echo $counts['content']; ?></sup></strong>
			</label>    
			<?php if (!empty($cats['content'])){ ?>
				<div class="sub-fields cats-mode">
					<label>
						<input type="radio" class="cats-mode" name="catsmode[content]" data-module="content" value="0" checked="checked" > 
						Все категории как один тип контента
					</label>
					<label>
						<input type="radio" class="cats-mode" name="catsmode[content]" data-module="content" value="1"> 
						Некоторые категории отдельными типами контента
					</label>
					<div class="sub-fields sub-fields-cats">
						<?php foreach($cats['content'] as $cat){ ?>
							<ul class="cats-list">
								<li class="c-id"><input type="checkbox" name="cats[content]" id="cats-content-<?php echo $cat['id']; ?>" value="<?php echo $cat['id']; ?>"></li>
								<li class="c-title"><label for="cats-content-<?php echo $cat['id']; ?>"><?php echo $cat['title']; ?></label></li>
								<li class="c-slug"><input type="text" name="cats_slugs[content]" placeholder="URL" value=""></li>
							</ul>
						<?php } ?>
					</div>
				</div>
			<?php } ?>
        </div>
        <div class="field module-blogs">
            <label>
				<input type="checkbox" class="module" data-module="blogs"  checked="checked" value="1"> 
				<strong>Посты в блогах <sup><?php echo $counts['blogs']; ?></sup></strong>
			</label>        
			<div class="sub-fields">
				<label>
					<input type="checkbox" class="option" data-module="blogs" data-option="convert" value="1" checked="checked" > 
					Преобразовать коллективные блоги в группы
				</label>
			</div>
        </div>
        <div class="field module-board">
            <label>
				<input type="checkbox" class="module" data-module="board" checked="checked" value="1"> 
				<strong>Доска объявления <sup><?php echo $counts['board']; ?></sup></strong>
			</label>            
        </div>
        <div class="field module-catalog">
            <label>
				<input type="checkbox" class="module" data-module="catalog" checked="checked" value="1"> 
				<strong>Универсальный каталог <sup><?php echo $counts['catalog']; ?></sup></strong>
			</label>
			<?php if(!empty($cats['catalog'])){ ?>
				<div class="sub-fields cats-mode">
					<label>
						<input type="radio" name="catsmode[catalog]" class="cats-mode" data-module="catalog" value="0" checked="checked" > 
						Все категории как один тип контента
					</label>
					<label>
						<input type="radio" name="catsmode[catalog]" class="cats-mode" data-module="catalog" value="1"> 
						Некоторые категории отдельными типами контента
					</label>
					<div class="sub-fields sub-fields-cats">
						<?php foreach($cats['catalog'] as $cat){ ?>
							<ul class="cats-list">
								<li class="c-id"><input type="checkbox" name="cats[catalog]" id="cats-catalog-<?php echo $cat['id']; ?>" value="<?php echo $cat['id']; ?>"></li>
								<li class="c-title"><label for="cats-catalog-<?php echo $cat['id']; ?>"><?php echo $cat['title']; ?></label></li>
								<li class="c-slug"><input type="text" name="cats_slugs[catalog]" placeholder="URL"></li>
							</ul>
						<?php } ?>
					</div>
				</div>		
			<?php } ?>
        </div>
        <div class="field module-faq">
            <label>
				<input type="checkbox" class="module" data-module="faq" checked="checked" value="1"> 
				<strong>Вопросы и ответы <sup><?php echo $counts['faq']; ?></sup></strong>
			</label>            
        </div>
        <div class="field module-clubs">
            <label>
				<input type="checkbox" class="module" data-module="clubs" checked="checked" value="1"> 
				<strong>Клубы (группы) <sup><?php echo $counts['clubs']; ?></sup></strong>
			</label>            
        </div>
        <div class="field module-photos">
            <label>
				<input type="checkbox" class="module" data-module="photos" checked="checked" value="1"> 
				<strong>Фотоальбомы <sup><?php echo $counts['photos']; ?></sup></strong>
			</label>            
        </div>

</form>

<div class="buttons">
    <input type="button" name="next" id="btn-next" value="Далее" onclick="submitSelect()" />
</div>

<script>
    $('.sub-fields-cats').hide();
    $('.cats-mode input:radio').click(function(){
        if ($(this).val()==1){
            $(this).parent('label').parent('.sub-fields').find('.sub-fields-cats').show();
        } else {
            $(this).parent('label').parent('.sub-fields').find('.sub-fields-cats').hide();
        }
    })
    
    <?php foreach($counts as $module=>$count){ ?>
        counts.<?php echo $module; ?> = <?php echo $count; ?>;
    <?php } ?>
</script>