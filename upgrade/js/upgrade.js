var modules = ['start', 'users'];
var options = {};
var cats = {};
var counts = {};
var limit = 3;
var current_module;
var current_step;
var current_step_id = 0;
var current_step_count = 0;
var rows_migrated = 0;
var steps;

function startMigration(){
    nextMigrationModule();
}

function nextMigrationModule(){
    
    if (modules.length==0){
        addLog('Миграция завершена');
        addLog('');
        $('.migrate-log li:last-child').hide();
        $('.buttons').show();
        return;
    }
    
    current_module = modules.shift();
    
    $.post('process.php', {module: current_module, step: 'list'}, function(result){
        
        steps = result;
        current_step_id = 0;
        current_step = null;
        nextMigrationStep();
        
    }, 'json');
    
}

function nextMigrationStep(){
    
    if (steps.length == 0){
        nextMigrationModule();
        return;
    }
    
    current_step = steps.shift();
    current_step_count = 0;
    rows_migrated = 0;
    
    console.log(current_step);
    
    var log = current_step.title;           
    
    if (current_step.is_count){        
        current_step_count = current_step.count;
        log = current_step.title + ' &mdash; <span class="count">0</span> из <span class="total">'+current_step_count+'</span>...';
    }
    
    addLog(log);
	
	var post_cats = '';
	var post_options = '';
    
	if (typeof(cats[current_module]) != 'undefined'){
		post_cats = JSON.stringify(cats[current_module]);
	}
	
	if (typeof(options[current_module]) != 'undefined'){
		post_cats = JSON.stringify(options[current_module]);
	}
	
    $.post('process.php', {module: current_module, step: current_step_id, from: 0, cats: post_cats, options: post_options}, function(result){               
        
        if (current_step.is_count){
            rows_migrated += result.rows;
            console.log("rows  = " + result.rows);
            $('.migrate-log li:last-child .count').html(rows_migrated);
            if (rows_migrated < current_step_count){
                repeatMigrationStep();
            } else {
                current_step_id++;
                nextMigrationStep();
            }
        }
        
        if (!current_step.is_count){
            current_step_id++;                
            nextMigrationStep();
        }
        
    }, 'json');
    
}

function repeatMigrationStep(){
    
    $.post('process.php', {module: current_module, step: current_step_id, from: rows_migrated}, function(result){               
        
        rows_migrated += result.rows;
        console.log("rows  = " + result.rows);
        $('.migrate-log li:last-child .count').html(rows_migrated);
        
        if (rows_migrated < current_step_count){
            repeatMigrationStep();
        } else {
            current_step_id++;
            nextMigrationStep();
        }
        
    }, 'json');
    
}

function addLog(text){
    $('.migrate-log').append('<li>' + text + '</li>');
}

function submitSelect(){
    
    $('input.module:checked').each(function(){
       
       var module = $(this).data('module');
       modules.push(module);
       
    });
    
    $('input.cats-mode:checked').each(function(){
       
       if ($(this).val()==0){return;}
       
       var module = $(this).data('module');
       
       cats[module] = [];
       
       $('.module-'+module+' .cats-list').each(function(){
           var id = $('.c-id input:checked', this).val();
           if (!id) {return;}
           var slug = $('.c-slug input', this).val();
		   var title = $('.c-title label', this).html();
           cats[module].push({'id': id, 'slug': slug, 'title': title});
       })
       
    });
    
    $('input.option:checked').each(function(){
       
       var module = $(this).data('module');
       var option = $(this).data('option');
       
       if (typeof(options[module])=='undefined') {options[module] = {}}
       
       options[module][option] = 1;
       
    });
    
    console.log(modules);
    console.log(cats);
    console.log(options);
    console.log(counts);
    
    nextStep();
    
}
