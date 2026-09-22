export class GameManager {
    constructor() {
        this.score = 0;
        this.hp = 5;
        this.wave = 1;
        this.ammo = 30;
        this.isRunning = false;
        this.targetFound = false;
        this.maxHp = 5;
        this.gameOver = false;
    }

    reset() {
        this.score = 0;
        this.hp = this.maxHp;
        this.wave = 1;
        this.ammo = 30;
        this.isRunning = false;
        this.targetFound = false;
        this.gameOver = false;
        console.log('Game state reset.');
    }

    addScore(points) {
        this.score += points;
    }

    damagePlayer() {
        this.hp = Math.max(0, this.hp - 1);
        return this.hp <= 0;
    }

    nextWave() {
        this.wave += 1;
    }
}