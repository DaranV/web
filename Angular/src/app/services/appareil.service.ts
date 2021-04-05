export class AppareilService {
	appareils = [
		{
			name: 'machine',
			status: 'éteint'
		},
		{
			name: 'frigo',
			status: 'allumé'
		},
		{
			name: 'ordi',
			status: 'éteint'
		}
	];	


	switchOnAll(){
		for(let ap of this.appareils){
			ap.status =  'allumé';
		}
	}

	switchOffAll(){
		for(let ap of this.appareils){
			ap.status = 'éteint';
		}
	}

	switchOnOne(i: number){
		this.appareils[i].status = 'allumé';
	}

	switchOffOne(i: number){
		this.appareils[i].status = 'éteint';
	}


}