import { BrowserModule } from '@angular/platform-browser';
import { NgModule } from '@angular/core';

import { AppRoutingModule } from './app-routing.module';
import { AppComponent } from './app.component';
import { AppareilComponent } from './appareil/appareil.component';
import { AppareilService } from './services/appareil.service';

@NgModule({
  declarations: [
    AppComponent,
    AppareilComponent
  ],
  imports: [
    BrowserModule,
    AppRoutingModule
  ],
  providers: [
  AppareilService
  ],
  bootstrap: [AppComponent]

})
export class AppModule { }
