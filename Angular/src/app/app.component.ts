import { Component, OnInit, Input } from '@angular/core';
import { AppareilService } from './services/appareil.service';

@Component({
  selector: 'app-root',
  templateUrl: './app.component.html',
  styleUrls: ['./app.component.scss']
})

export class AppComponent {
	isAuth = false;

	lastupdate = new Promise((resolve, reject) => {
	    const date = new Date();
	    setTimeout(
	      () => {
	        resolve(date);
	      }, 2000
	    );
	  });


	appareils: any[];

	constructor(private appareilService: AppareilService){
		setTimeout(
			() => {
				this.isAuth = true;
			}, 4000
		);
	}

	ngOnInit(){
		this.appareils = this.appareilService.appareils;
	}

	onAllumer(){
		this.appareilService.switchOnAll();
	}

	onEteindre(){
		if(confirm('Êtes-vous sûr de vouloir tout éteindre ?')){
			this.appareilService.switchOffAll();			
		} else{
			return null;
		}
	}



}
