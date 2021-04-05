import { Component, OnInit, Input } from '@angular/core';
import { AppareilService } from '../services/appareil.service';


@Component({
  selector: 'app-appareil',
  templateUrl: './appareil.component.html',
  styleUrls: ['./appareil.component.scss']
})

export class AppareilComponent implements OnInit {

  @Input() index: number;
  @Input() appareilName: string;
  @Input() appareilStatus: string;	

  constructor(private appareilService: AppareilService) { }

  ngOnInit() {
  }

  onSwitch(){
    if(this.appareilStatus ==='allumé'){
      this.appareilService.switchOffOne(this.index);
    } else if(this.appareilStatus === 'éteint'){
      this.appareilService.switchOnOne(this.index);
    }
  }

  getStatus(){
  	return this.appareilStatus;
  }

  getcolor(){
  	if (this.appareilStatus === 'allumé'){
  		return 'green';
  	} else if(this.appareilStatus === 'éteint'){
  		return 'red';
  	}
  }

}
