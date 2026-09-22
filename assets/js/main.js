import * as THREE from 'three';
import { MindARThree } from 'mindar-image-three';
import { GameManager } from './game.js';
import { WeaponSystem } from './weapon.js';
import { EnemyManager } from './enemy.js';
import { EffectsSystem } from './effects.js';
import { AudioSystem } from './audio.js';

if (!('outputColorSpace' in THREE.WebGLRenderer.prototype) && 'sRGBEncoding' in THREE) {
    Object.defineProperty(THREE.WebGLRenderer.prototype, 'outputEncoding', {
        get() {
            return this.outputColorSpace === THREE.SRGBColorSpace ? THREE.sRGBEncoding : this.outputColorSpace;
        },
        set(value) {
            this.outputColorSpace = value === THREE.sRGBEncoding ? THREE.SRGBColorSpace : value;
        },
        configurable: true
    });
}

class App {
    constructor() {
        this.game = new GameManager();
        this.audio = new AudioSystem();
        this.raycaster = new THREE.Raycaster();
        this.mouse = new THREE.Vector2(0, 0);
        this.clock = new THREE.Clock();
    }

    async init() {
        const res = await fetch('api/models.php');
        const data = await res.json();
        
        // Support arrays or single strings for models
        const enemyUrls = data.enemyUrls || [data.enemyUrl || 'assets/models/orc-enemy.glb'];
        const weaponUrls = data.weaponUrls || [data.weaponUrl || 'assets/models/fps-akm.glb'];

        this.mindarThree = new MindARThree({
            container: document.body,
            imageTargetSrc: 'assets/targets/targets.mind',
            uiLoading: 'yes',
            uiScanning: 'no',
            uiError: 'yes'
        });

        const { renderer, scene, camera } = this.mindarThree;
        
        this.overlayScene = new THREE.Scene();
        this.overlayCamera = new THREE.PerspectiveCamera(60, window.innerWidth / window.innerHeight, 0.1, 10.0);

        const ambientLight = new THREE.AmbientLight(0xffffff, 1.2);
        scene.add(ambientLight);
        const hemiLight = new THREE.HemisphereLight(0xffffff, 0x444444, 1.0);
        hemiLight.position.set(0, 10, 0);
        scene.add(hemiLight);

        const overlayAmbient = new THREE.AmbientLight(0xffffff, 1.5);
        this.overlayScene.add(overlayAmbient);
        const overlayDirLight = new THREE.DirectionalLight(0xffffff, 1.5);
        overlayDirLight.position.set(0, 5, 5);
        this.overlayScene.add(overlayDirLight);

        const anchor = this.mindarThree.addAnchor(0);
        this.anchorGroup = anchor.group;

        this.enemyMgr = new EnemyManager(this.anchorGroup);
        await this.enemyMgr.loadTemplates(enemyUrls);

        this.weapon = new WeaponSystem(camera, this.overlayScene);
        await this.weapon.load(weaponUrls);

        this.effects = new EffectsSystem(this.anchorGroup);

        anchor.onTargetFound = () => {
            this.game.targetFound = true;
            document.getElementById('targetStatus').innerText = 'TARGET LOCKED';
            document.getElementById('targetStatus').style.color = '#00ffcc';
            if (!this.game.isRunning) this.startGame();
            if (this.enemyMgr.enemies.length === 0) this.spawnWaveEnemies();
        };

        anchor.onTargetLost = () => {
            this.game.targetFound = false;
            document.getElementById('targetStatus').innerText = 'TARGET LOST';
            document.getElementById('targetStatus').style.color = '#ffcc00';
        };

        const fireBtn = document.getElementById('fireBtn');
        const reloadBtn = document.getElementById('reloadBtn');
        const switchBtn = document.getElementById('switchGunBtn'); // Make sure you have this button in HTML
        const restartBtn = document.getElementById('restartBtn');

        fireBtn.addEventListener('pointerdown', (e) => { e.preventDefault(); this.shoot(); });
        reloadBtn.addEventListener('pointerdown', (e) => { e.preventDefault(); this.reloadWeapon(); });
        if (switchBtn) {
            switchBtn.addEventListener('pointerdown', (e) => { e.preventDefault(); this.switchGun(); });
        }
        restartBtn.addEventListener('click', () => { this.restartGame(); });

        window.addEventListener('keydown', (e) => {
            if (e.code === 'Space' || e.code === 'KeyF') { e.preventDefault(); this.shoot(); }
            if (e.code === 'KeyR') { e.preventDefault(); this.reloadWeapon(); }
            if (e.code === 'KeyQ') { e.preventDefault(); this.switchGun(); }
        });

        try {
            await this.mindarThree.start();
        } catch (error) {
            console.warn('MindAR camera start failed.', error);
            return;
        }

        this.game.reset();
        this.startGame();

        renderer.autoClear = false;
        this.mindarThree.renderer.setAnimationLoop(() => {
            const dt = Math.min(this.clock.getDelta(), 0.05);
            this.update(dt);
            
            renderer.clear();
            renderer.render(scene, camera);
            renderer.clearDepth();
            renderer.render(this.overlayScene, this.overlayCamera);
        });

        window.addEventListener('resize', () => {
            this.overlayCamera.aspect = window.innerWidth / window.innerHeight;
            this.overlayCamera.updateProjectionMatrix();
        });
    }

    startGame() {
        this.game.isRunning = true;
        document.getElementById('gameOverModal').style.display = 'none';
        this.spawnWaveEnemies();
    }

    spawnWaveEnemies() {
        const livingEnemies = this.enemyMgr.enemies.filter(e => !e.isDead);
        if (livingEnemies.length === 0) {
            this.enemyMgr.clear();
            this.enemyMgr.spawn(this.game.wave);
        }
        this.updateHUD();
    }

    showHitCompleteNotification() {
        let notif = document.getElementById('hitCompleteNotif');
        if (!notif) {
            notif = document.createElement('div');
            notif.id = 'hitCompleteNotif';
            notif.style.position = 'absolute';
            notif.style.top = '25%';
            notif.style.left = '50%';
            notif.style.transform = 'translate(-50%, -50%)';
            notif.style.color = '#00ffcc';
            notif.style.fontSize = '24px';
            notif.style.fontWeight = 'bold';
            notif.style.textShadow = '0 0 10px rgba(0,255,204,0.8)';
            notif.style.pointerEvents = 'none';
            notif.style.zIndex = '999';
            document.body.appendChild(notif);
        }
        notif.innerText = 'HIT COMPLETE!';
        notif.style.display = 'block';
        setTimeout(() => {
            notif.style.display = 'none';
        }, 600);
    }

    shoot() {
        if (!this.game.isRunning) return;
        if (!this.weapon.canFire()) {
            if (this.weapon.ammo <= 0) this.reloadWeapon();
            return;
        }

        this.audio.playShoot();
        this.weapon.fire();
        this.updateHUD();

        const crosshair = document.getElementById('crosshair');
        crosshair.classList.add('fire');
        setTimeout(() => crosshair.classList.remove('fire'), 100);

        const activeEnemies = this.enemyMgr.enemies.filter(e => !e.isDead);
        let enemyObj = null;

        this.mouse.set(0, 0);
        this.raycaster.setFromCamera(this.mouse, this.mindarThree.camera);
        
        for (const e of activeEnemies) {
            if (this.raycaster.ray.intersectsBox(e.box)) {
                enemyObj = e;
                break;
            }
        }

        if (!enemyObj && activeEnemies.length > 0) {
            enemyObj = activeEnemies[0];
        }

        if (enemyObj) {
            enemyObj.hp--;
            this.audio.playHit();
            this.effects.spawnHitSpark(enemyObj.mesh.position);
            
            this.enemyMgr.flashWhite(enemyObj);
            this.showHitCompleteNotification();

            crosshair.classList.add('hit');
            setTimeout(() => crosshair.classList.remove('hit'), 150);

            if (enemyObj.hp <= 0 && !enemyObj.isDead) {
                const idx = this.enemyMgr.enemies.indexOf(enemyObj);
                if (idx > -1) {
                    this.enemyMgr.triggerDeath(idx);
                    this.game.addScore(100 + this.game.wave * 25);
                }
                
                this.game.wave++;
                setTimeout(() => {
                    this.spawnWaveEnemies();
                }, 1500);
            }
        }
    }

    reloadWeapon() {
        this.audio.playReload();
        this.weapon.reload(() => this.updateHUD());
    }

    switchGun() {
        this.weapon.switchWeapon();
        this.updateHUD();
    }

    update(dt) {
        this.weapon.update(dt);
        this.effects.update(dt);

        if (this.game.isRunning && this.game.targetFound) {
            const playerPos = new THREE.Vector3(0, 0, 0);
            this.enemyMgr.update(
                dt, 
                playerPos, 
                () => {
                    this.audio.playDamage();
                    const isDead = this.game.damagePlayer();
                    this.updateHUD();
                    
                    if (isDead) {
                        this.gameOver();
                    } else {
                        setTimeout(() => this.spawnWaveEnemies(), 500);
                    }
                }, 
                (distanceVal) => {
                    const bar = document.getElementById('distanceBar');
                    if (bar) {
                        const maxDist = 1.5;
                        const minDist = 0.35;
                        const clampedDist = Math.max(minDist, Math.min(maxDist, distanceVal));
                        const percentage = ((clampedDist - minDist) / (maxDist - minDist)) * 100;
                        bar.style.width = percentage + '%';
                    }
                }
            );
        }
    }

    updateHUD() {
        document.getElementById('scoreVal').innerText = this.game.score;
        document.getElementById('waveVal').innerText = this.game.wave;
        document.getElementById('hpVal').innerText = this.game.hp;
        document.getElementById('ammoVal').innerText = `${this.weapon.ammo}/${this.weapon.maxAmmo}`;
    }

    gameOver() {
        this.game.isRunning = false;
        document.getElementById('finalScore').innerText = this.game.score;
        document.getElementById('finalWave').innerText = this.game.wave;
        document.getElementById('gameOverModal').style.display = 'flex';
    }

    restartGame() {
        this.enemyMgr.clear();
        this.game.reset();
        this.weapon.ammo = this.weapon.maxAmmo;
        this.spawnWaveEnemies();
        document.getElementById('gameOverModal').style.display = 'none';
    }
}

window.addEventListener('DOMContentLoaded', () => {
    const app = new App();
    app.init().catch(err => console.error(err));
});